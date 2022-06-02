<?php declare(strict_types=1);

namespace App\Event\Listener;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    /** @var string[] */
    protected array $locales;
    protected string $cookie;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->locales = $parameters->get('locales') ?? [];
        $this->cookie = $parameters->get('locale_cookie') ?: 'locale';
    }

    #[ArrayShape([KernelEvents::REQUEST => "array[]", KernelEvents::RESPONSE => "\string[][]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 20],
            ],
            KernelEvents::RESPONSE => [
                ['onKernelResponse'],
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $this->getLocaleFromCookie($request)
            ?: $this->getLocaleFromHeader($request)
                ?: $this->getLocaleFromUrl($request)
                    ?: $request->getDefaultLocale();

        $request->setLocale($locale);
        $request->query->add(['locale' => $locale]);
    }

    protected function getLocaleFromCookie(Request $request): ?string
    {
        return $this->filterLocale($request->cookies->get($this->cookie));
    }

    protected function filterLocale(?string $locale): ?string
    {
        return in_array($locale, $this->locales) ? $locale : null;
    }

    protected function getLocaleFromHeader(Request $request): ?string
    {
        return $this->filterLocale($request->getPreferredLanguage());
    }

    protected function getLocaleFromUrl(Request $request): ?string
    {
        $localePattern = sprintf('@^(/)?(%s)/.+?(/)?$@', implode('|', $this->locales));
        $urlPath = parse_url($request->query->get('url') ?? '', PHP_URL_PATH);
        $matches = [];
        if (!preg_match($localePattern, $urlPath, $matches)) {
            return null;
        }
        $locale = $matches[2] ?? null;

        return $this->filterLocale($locale);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $event->getResponse()->headers->add(['content-language' => $event->getRequest()->getLocale()]);
    }
}
