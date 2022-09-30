<?php

namespace Drupal\commerce_cart_redirection\EventSubscriber;

use Drupal\commerce_cart\Event\CartEntityAddEvent;
use Drupal\commerce_cart\Event\CartEvents;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Drupal\Component\Utility\UrlHelper;

class CommerceCartRedirectionSubscriber implements EventSubscriberInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The route provider to load routes by name.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * Config for the module.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * CartEventSubscriber constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider.
   */
  public function __construct(RequestStack $request_stack, RouteProviderInterface $route_provider) {
    $this->requestStack = $request_stack;
    $this->routeProvider = $route_provider;
    $this->config = \Drupal::config('commerce_cart_redirection.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      CartEvents::CART_ENTITY_ADD => 'tryRedirect',
      KernelEvents::RESPONSE => ['checkRedirectIssued', -10],
    ];
    return $events;
  }

  /**
   * Conditionally skip cart and send user to checkout.
   *
   * If the added product meets criteria set in the config form then redirect.
   *
   * @param \Drupal\commerce_cart\Event\CartEntityAddEvent $event
   *   The add to cart event.
   */
  public function tryRedirect(CartEntityAddEvent $event) {
    $redirect = FALSE;
    $purchased_entity = $event->getEntity();
    $active_bundles = $this->config->get('product_bundles');
    $negate = $this->config->get('negate_product_bundles');
    $purchased_bundle = $purchased_entity->bundle();

    if (isset($active_bundles[$purchased_bundle]) && $active_bundles[$purchased_bundle] !== 0) {
      if (!$negate) {
        $redirect = TRUE;
      }
    }
    else {
      if ($negate) {
        $redirect = TRUE;
      }
    }

    if ($redirect) {
      if ($this->config->get('clear_cart_before_add')) {
        $this->clearCartAndAdd($event);
      }
      $redirection_url = $this->getRedirectionUrl($event);
      $this->requestStack->getCurrentRequest()->attributes
        ->set('commerce_cart_redirection_url', $redirection_url);
    }
  }


  /**
   * Checks if a redirect url has been set.
   *
   * Redirects to the provided url if there is one.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event.
   */
  public function checkRedirectIssued(FilterResponseEvent $event) {
    $request = $event->getRequest();
    $redirect_url = $request->attributes->get('commerce_cart_redirection_url');
    if (isset($redirect_url)) {
      $event->setResponse(new RedirectResponse($redirect_url));
    }
  }


  /**
   * Clear the existing content of the cart and add the new item.
   *
   * If the item being added already exists in the cart this will set the
   * quantity in the cart to the quantity in the current request.
   *
   * @param $event
   *
   * @return void
   */
  private function clearCartAndAdd($event) {
    $cart = $event->getCart();
    $current_order_item = $event->getOrderItem();
    $order_items = $cart->getItems();
    foreach ($order_items as $order_item) {
      if ($current_order_item->id() !== $order_item->id()) {
        $order_item->delete();
      } else {
        $current_order_item->setQuantity($event->getQuantity())->save();
      }
    }

    $cart->setItems([$current_order_item]);
    $cart->setAdjustments([]);
  }

  /**
   * Get the url that we redirect to after add to cart event.
   *
   * @param $event
   *
   * @return string
   */
  private function getRedirectionUrl($event):string {
    // Set a default so we always end up somewhere.
    $redirection_url = Url::fromRoute('<front>')->toString();

    // Since the hard dependency on commerce_checkout module has been removed
    // in order to be compatible with any module that provides its
    // own checkout, eg BigCommerce, we can not guarantee that this route exists.
    // If it does we use it as our fall back instead of the home page.
    if (count($this->routeProvider->getRoutesByNames(['commerce_checkout.form'])) === 1) {
      $redirection_url = Url::fromRoute('commerce_checkout.form', [
        'commerce_order' => $event->getCart()->id(),
      ])->toString();
    }
    switch ($this->config->get('redirection_route_path')) {
      case 'other':
        // @TODO add some sort of sanity checking.
        if (!empty($this->config->get('redirection_route_path_other')) && UrlHelper::isValid($this->config->get('redirection_route_path_other'))) {
          $redirection_url = $this->config->get('redirection_route_path_other');
        }
        break;
      case 'cart':
        $redirection_url = Url::fromRoute('commerce_cart.page')->toString();
        break;
      case'checkout':
      default:
        // Already set above, no need to duplicate.
    }

    return $redirection_url;
  }

}
