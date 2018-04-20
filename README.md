# seo-bundle

For Sylius project see: ...
For Changmin project see: ...

## 1. ติดตั้ง
composer.json
```json
"require": {
   "intbizth/seo-bundle": "dev-master"
}
"repositories": [      
   {
      "type": "vcs",
      "url": "https://github.com/intbizth/seo-bundle"
   }
],
```
AppKernel.php
```php
  $bundles = [
    ...
    new Presta\SitemapBundle\PrestaSitemapBundle(),
    new \Toro\SeoBundle\ToroSeoBundle(),
    ...
  ];
```

```yaml
# config.yml
imports:
    - { resource: "@ToroSeoBundle/Resources/config/app/main.yml" }
```


## 2. Sitemap
### 2.1 Static Route
```yaml
# routing.yml
presta_sitemap:
    resource: "@PrestaSitemapBundle/Resources/config/routing.yml"
```

```yaml
# config.yml
toro_seo:
    sitemap_routing:
        - 
            route: _homepage
            parameters: []
            priority: 0.7
            changefreq: daily # "always", "hourly", "daily", "weekly", "monthly", "yearly", "never"            

```
จากนั้นลองเปิด url /sitemap.xml

### 2.2 Resources Route
เรามี `AbstractSitemapListener.php` ให้ ตัวอย่าง `Page` resource
```php
<?php
// AppBundle\PageSitemapListener.php

use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Toro\SeoBundle\Provider\LocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class PageSitemapListener extends \Toro\SeoBundle\Sitemap\AbstractSitemapListener
{
    const SECTION = 'cms_page';
    
    /**
     * @var PageRepositoryInterface
     */
    private $repository;

    public function __construct(UrlGeneratorInterface $urlGenerator, PageRepositoryInterface $repository)
    {
        parent::__construct($urlGenerator);
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function registerUrls(UrlContainerInterface $urls): void
    {
        $pages = $this->repository->findBy(['enabled' => true]);
        /** @var PageInterface $page */
        foreach ($pages as $page) {
            $translations = $page->getTranslations();
            $isMultiLang = 2 <= count($translations);
            $url = new UrlConcrete(
                $this->urlGenerator->generate(
                    'page_by_slug',
                    [
                        'slug' => $page->getSlug(),
                        '_locale' => $page->getTranslation()->getLocale()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                $page->getUpdatedAt() ?: $page->getCreatedAt(),
                'weekly',
                0.8
            );

            if (!$isMultiLang) {
                $urls->addUrl($url, self::SECTION);
                continue;
            }

            $url = new GoogleMultilangUrlDecorator($url);
            /** @var PageTranslationInterface $translation */
            foreach ($translations as $translation) {
                $url->addLink($this->urlGenerator->generate(
                    'page_by_slug',
                    [
                        'slug' => $translation->getSlug(),
                        '_locale' => $translation->getLocale()
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ), $translation->getLocale());
            }

            $urls->addUrl($url, self::SECTION);
        }
    }
}

```
```xml
# services.xml
# parent is required
<service parent="toro_seo.listener.abstract_sitemap_listener" class="AppBundle\PageSitemapListener">
     <argument type="service" id="page.repository" />
     <tag name="kernel.event_subscriber" />
</service>
```

### 2.3 Sitemap Locale
จาก `AbstractSitemapListener.php` จะใช้ `LocaleProvider` จาก config by default การเพิ่มภาษาคือ config ดังนี้
```yaml
#config.yml
toro_seo:
    sitemap_locale:
        default_locale: 'th'
        all_locales: ['th', 'en']
```
#### 2.3.1  Custom Locale Provider
```yaml
#config.yml
toro_seo:
    sitemap_locale:
        service: your.service.id # implement Toro\SeoBundle\Provider\LocaleProviderInterface
```

## 3. Metadata Seo
แนวคิดคือ การใส่ seo ควรจะใส่ได้จากหลังบ้าน เพราะฉะนั้น มี 2 criteria สำหรับ metadata คือ `pathinfo` (Admin) , `route_name and params` (DEV)

### 3.1 วิธีการใช้งานผ่าน twig คือ

```twig
  <!DOCTYPE html>
<html>
    <head>
        {% block metadata %}
            {{ toro_seo_render(app.request, {
                template: '@Appbundle/your_template.html.twig' # default @ToroSeo/_web_meta_seo.html.twig
                default_title: 'My title',
                default_keywords: 'keyword, keyword2',
                default_description: 'My description',
                options: {
                    test: 1 # อะไรก็ได้ จะถูกส่งต่อไปยัง template
                }
            }) }}
        {% endblock %}
    </head>
    <body>  
    </body>
</html>

```

ใน template ที่สร้าง จะมี 
- `toro_seo` ประกอบด้วย title, description, keywords. image_url 
- `options` ที่ส่งมาจาก twig
ตัวอย่างอยู่ที่ไฟล์ `@ToroSeo/_web_meta_seo.html.twig`


### 3.2 Model
มี 2 วิธีการเลือกใช้คือ 
1. `pathinfo` คือให้ admin สามารถใส่ url หน้าที่เราต้องการได้ เช่น กรณีหน้า `https://www.example.com/th/contact-us` ให้เราใส่ `pathinfo` เป็น
`/contact-us` 
> NOTE /{_locale} ไม่ต้องใส่

2. `route` และ `parameters` ให้สำหรับ dev คือใส่ชื่อ routing และ params ในหน้านั้นๆ

**ความสำคัญ pathinfo > route and parameters**

### 3.3 Image Provider
คือปกติแล้ว แต่ละ โปรเจคจะมีวิธีการ get รูปภาพไม่เหมือนกัน ฉะนั้นจึงต้องสร้าง `ImageProvider` ขึ้นมาโดย implement `Toro\SeoBundle\Provider\ImageProviderInterface.php` เช่น
```php
// LiipImageProvider.php
<?php

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Toro\SeoBundle\Model\MetaSeoInterface;
use Toro\SeoBundle\Provider\ImageProviderInterface;

final class SyliusImageProvider implements ImageProviderInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function getImagePath(MetaSeoInterface $metaSeo): ?string
    {  
        return $this->cacheManager->getBrowserPath($this->getImage($metaSeo), '200x200') : null;
    }
    
    private function getImage($metaSeo) 
    {
        return // implement get your image
    }
}
```

```xml
# services.xml
<service id="app.provider.liip_image" class="AppBundle\LiipImageProvider">
    <argument id="liip_imagine.cache.manager" type="service" />
</service>

```

```yaml
# config.yml
toro_seo:
    renderer:
        image_provider: "app.provider.liip_image"

```

# Configulation Ref.

```yaml
toro_seo:
    renderer: 
        service: 'toro_seo.twig_extension.render_meta_extension' # custom renderer
        image_provider: 'some.image.provider.service' # required if use renderer "toro_seo.twig_extension.render_meta_extension"
        cache: 'some.cache.service' # implement `Psr\Cache\CacheItemPoolInterface`
    sitemap_locale:
        service: 'toro_seo.locale_provider.configuration' # custom locale provider
        default_locale: "%locale%" # required if use provider "toro_seo.locale_provider.configuration"
        all_locales: ['th', 'en'] # required if use provider "toro_seo.locale_provider.configuration"
    ignore_request_attrs: ['template', '_locale', '_sylius'] # Ignore route params matching in renderer   
    sitemap_routing:
        - 
            route: route_name
            parameters: []
            priority: 1.0
            changefreq: daily
        - 
            route: route_name_2
            parameters: { param1: 'test' }
            priority: 0.5
            changefreq: daily

```
