<?php

namespace App\Modules\Notification\Services;

/**
 * TemplateEngine - جایگزینی متغیرها در قالب‌ها
 * 
 * پشتیبانی از:
 * - {{variable}}
 * - {{user.name}}
 * - {{order.code}}
 * - {{#if condition}}...{{/if}}
 */
class TemplateEngine
{
    /**
     * جایگزینی متغیرها در قالب
     * 
     * @param string $template قالب با {{variable}}
     * @param array $variables متغیرها
     * @return string قالب پر شده
     */
    public function render(string $template, array $variables): string
    {
        // 1. پردازش شرط‌ها (اگر باشد)
        $result = $this->processConditionals($template, $variables);

        // 2. جایگزینی متغیرها
        $result = preg_replace_callback('/\{\{([^}]+)\}\}/', function ($matches) use ($variables) {
            $key = trim($matches[1]);
            return $this->resolveVariable($key, $variables);
        }, $result);

        return $result;
    }

    /**
     * پردازش شرط‌های ساده
     */
    private function processConditionals(string $template, array $variables): string
    {
        // {{#if variable}}content{{/if}}
        $pattern = '/\{\{#if\s+([^}]+)\}\}(.*?)\{\{\/if\}\}/s';
        
        return preg_replace_callback($pattern, function ($matches) use ($variables) {
            $varName = trim($matches[1]);
            $content = $matches[2];
            $value = $this->resolveVariable($varName, $variables);
            
            return !empty($value) ? $content : '';
        }, $template);
    }

    /**
     * حل یک متغیر (با پشتیبانی از dot notation)
     */
    private function resolveVariable(string $key, array $variables): string
    {
        // پشتیبانی از dot notation: user.name, order.code
        $keys = explode('.', $key);
        $value = $variables;

        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } elseif (is_object($value) && isset($value->$k)) {
                $value = $value->$k;
            } else {
                return ''; // متغیر یافت نشد
            }
        }

        // تبدیل به string
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return (string) $value;
    }

    /**
     * استخراج لیست متغیرهای استفاده شده در قالب
     */
    public function extractVariables(string $template): array
    {
        preg_match_all('/\{\{([^}#\/]+)\}\}/', $template, $matches);
        return array_unique(array_map('trim', $matches[1]));
    }

    /**
     * اعتبارسنجی قالب
     */
    public function validate(string $template, array $variables): array
    {
        $usedVariables = $this->extractVariables($template);
        $missing = [];

        foreach ($usedVariables as $var) {
            $keys = explode('.', $var);
            $value = $variables;
            $found = true;

            foreach ($keys as $k) {
                if (is_array($value) && array_key_exists($k, $value)) {
                    $value = $value[$k];
                } elseif (is_object($value) && isset($value->$k)) {
                    $value = $value->$k;
                } else {
                    $found = false;
                    break;
                }
            }

            if (!$found) {
                $missing[] = $var;
            }
        }

        return [
            'valid' => empty($missing),
            'missing_variables' => $missing,
            'used_variables' => $usedVariables,
        ];
    }
}
