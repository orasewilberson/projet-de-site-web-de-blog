<?php
namespace Framework\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class FormExtension extends AbstractExtension {

    public function getFunctions()
    {
        return [
            new TwigFunction('field', [$this, 'field'], ['is_safe' => ['html'], 'needs_content' => true])
        ];
    }

    /**
     * Genere le code HTML d'un champs
     * @param [type] $context contexte de la vue Twig
     * @param string $key clef du champs
     * @param [type] $value valeur du champs
     * @param string|null $label Label a utiliser
     * @param array $options
     * @return string
     */
    public function field($context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $error = $this->getErrorsHTML($context, $key);
        $class = 'form-group';
         $value = $this->convertValue($value);

        // Ajouter la classe 'form-control' sauf pour les checkboxes
        if ($type !== 'checkbox') {
            $attribute = trim('form-control ' . ($options['class'] ?? ''));
        } else {
            $attribute = $options['class'] ?? '';
        }

         // Définir les attributs de base
         $attributes = [
            'class' => $attribute,
            'name' => $key,
            'id' => $key
        ];

        if($error) {
            $class .= ' has-danger';
            $attributes['class'] .= ' is-invalid';
        }
        // Générer le bon type d'input
        if($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        }  elseif ($type === 'file') {
            $input = $this->file($attributes);
        } elseif ($type === 'checkbox') {
            $input = $this->checkbox($value, $attributes);
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }
        return "<div class=\"" . $class . "\">
            <label for=\"name\">{$label}</label>
            {$input}
            {$error}
        </div>";
    }

    private function convertValue($value): string
    {
        if($value instanceof \DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string)$value;
    }

    /**
     * Genere l'HTML en fonction des erreurs du contexte
     *
     * @param [type] $context
     * @param [type] $key
     * @return void
     */
    private function getErrorsHTML($context, $key) {
        $error = $context['errors'][$key] ?? false;
        if($error){
            return "<small class=\"invalid-feedback\">{$error}</small>";
        }
        return "";
    }

    /**
     * Genere un <input>
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"{$value}\">";   
    }

     /**
     * Genere un <input type="checkbox">
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function checkbox(?string $value, array $attributes): string
    {
        $html = '<input type="hidden" name="' . $attributes['name'] . '" value="0"/>';
        if($value) {
            $attributes['checked'] = true;
        }
        return $html . "<input type=\"checkbox\" " . $this->getHtmlFromArray($attributes) . " value=\"1\">";   
    }

    public function file(array $attributes)
    {
        return "<input type=\"file\" " . $this->getHtmlFromArray($attributes) . ">";    
    }

    /**
     * Genere un <textarea>
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";   
    }

    /**
     * Genere un <select>
     *
     * @param string|null $value
     * @param array $options
     * @param array $attributes
     * @return void
     */
    private function select(?string $value, array $options, array $attributes)
    {
        $htmlOptions = \array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";   


    }

    /**
     * Transforme un tableau $clef => $valeur en attribut HTML
     *
     * @param array $attributes
     * @return void
     */
    private function getHtmlFromArray(array $attributes)
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if($value === true){
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
            $htmlParts[] = "$key=\"$value\"";
            }
        }
    
        return implode(' ', $htmlParts);
    }
}

         
