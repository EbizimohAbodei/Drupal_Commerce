<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/ecwid_shopping_cart/templates/ec-store-connect.html.twig */
class __TwigTemplate_a83a1781d7cd070e839769d6374f212a85d0a6b1640c6f2b99af61ca1ce7e5b2 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div class=\"ec-store-box\">
    <div class=\"greeting-image\">
    </div>

    <div class=\"greeting-message mobile-br\">Connect your store<br> to this Drupal site</div>

    ";
        // line 7
        if ((($context["error"] ?? null) == "access_denied")) {
            // line 8
            echo "        <div class=\"note error\">
            Connection error - after clicking button you need to login and accept permissions to use our plugin. Please, try again.
        </div>
    ";
        }
        // line 12
        echo "    <div class=\"connect-button\">
        <a href=\"";
        // line 13
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["oauthUrl"] ?? null), 13, $this->source), "html", null, true);
        echo "\" class=\"button button-primary\">Connect</a>
    </div>
    
    
    <div class=\"note initial\">To display your store on this site, you need to allow Drupal to access your products. Please press connect to provide permission.</div>

    <div class=\"create-account-link\">
        <a target=\"_blank\" href=\"";
        // line 20
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["registerLink"] ?? null), 20, $this->source), "html", null, true);
        echo "\">Don't have an Ecwid account? Create one now.</a>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "modules/contrib/ecwid_shopping_cart/templates/ec-store-connect.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 20,  58 => 13,  55 => 12,  49 => 8,  47 => 7,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/ecwid_shopping_cart/templates/ec-store-connect.html.twig", "/app/web/modules/contrib/ecwid_shopping_cart/templates/ec-store-connect.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 7);
        static $filters = array("escape" => 13);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
