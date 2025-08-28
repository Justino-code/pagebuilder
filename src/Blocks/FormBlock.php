<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class FormBlock extends BaseBlock implements Block
{
    public static function type(): string
    {
        return 'form';
    }
    
    public static function label(): string
    {
        return 'Contact Form';
    }
    
    public static function icon(): string
    {
        return '📋';
    }
    
    public static function schema(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => 'Form Title',
                'default' => 'Contact Us',
                'required' => false
            ],
            'description' => [
                'type' => 'textarea',
                'label' => 'Description',
                'default' => 'Get in touch with us',
                'required' => false
            ],
            'email_recipient' => [
                'type' => 'email',
                'label' => 'Email Recipient',
                'default' => 'admin@example.com',
                'required' => true
            ],
            'submit_text' => [
                'type' => 'text',
                'label' => 'Submit Button Text',
                'default' => 'Send Message',
                'required' => true
            ],
            'success_message' => [
                'type' => 'text',
                'label' => 'Success Message',
                'default' => 'Thank you for your message! We will get back to you soon.',
                'required' => false
            ],
            'fields' => [
                'type' => 'repeater',
                'label' => 'Form Fields',
                'fields' => [
                    'type' => [
                        'type' => 'select',
                        'label' => 'Field Type',
                        'options' => [
                            'text' => 'Text',
                            'email' => 'Email',
                            'tel' => 'Phone',
                            'textarea' => 'Textarea',
                            'select' => 'Select',
                            'checkbox' => 'Checkbox'
                        ],
                        'default' => 'text',
                        'required' => true
                    ],
                    'name' => [
                        'type' => 'text',
                        'label' => 'Field Name',
                        'default' => '',
                        'required' => true,
                        'description' => 'Unique identifier (e.g., name, email)'
                    ],
                    'label' => [
                        'type' => 'text',
                        'label' => 'Field Label',
                        'default' => '',
                        'required' => true
                    ],
                    'placeholder' => [
                        'type' => 'text',
                        'label' => 'Placeholder',
                        'default' => '',
                        'required' => false
                    ],
                    'required' => [
                        'type' => 'checkbox',
                        'label' => 'Required',
                        'default' => false
                    ],
                    'options' => [
                        'type' => 'textarea',
                        'label' => 'Options (for select)',
                        'default' => '',
                        'description' => 'One option per line: value:Label',
                        'condition' => 'fields.*.type == "select"'
                    ]
                ],
                'default' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Your name',
                        'required' => true,
                        'options' => ''
                    ],
                    [
                        'type' => 'email',
                        'name' => 'email',
                        'label' => 'Email',
                        'placeholder' => 'your@email.com',
                        'required' => true,
                        'options' => ''
                    ],
                    [
                        'type' => 'textarea',
                        'name' => 'message',
                        'label' => 'Message',
                        'placeholder' => 'Your message',
                        'required' => true,
                        'options' => ''
                    ]
                ]
            ],
            'styles' => [
                'type' => 'style-group',
                'label' => 'Form Styles',
                'fields' => [
                    'background_color' => [
                        'type' => 'color',
                        'label' => 'Background Color',
                        'default' => '#ffffff'
                    ],
                    'text_color' => [
                        'type' => 'color',
                        'label' => 'Text Color',
                        'default' => '#000000'
                    ],
                    'button_color' => [
                        'type' => 'color',
                        'label' => 'Button Color',
                        'default' => '#3b82f6'
                    ],
                    'button_text_color' => [
                        'type' => 'color',
                        'label' => 'Button Text Color',
                        'default' => '#ffffff'
                    ]
                ]
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Contact Us',
            'description' => 'Get in touch with us',
            'email_recipient' => 'admin@example.com',
            'submit_text' => 'Send Message',
            'success_message' => 'Thank you for your message! We will get back to you soon.',
            'fields' => [
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Name',
                    'placeholder' => 'Your name',
                    'required' => true,
                    'options' => ''
                ],
                [
                    'type' => 'email',
                    'name' => 'email',
                    'label' => 'Email',
                    'placeholder' => 'your@email.com',
                    'required' => true,
                    'options' => ''
                ],
                [
                    'type' => 'textarea',
                    'name' => 'message',
                    'label' => 'Message',
                    'placeholder' => 'Your message',
                    'required' => true,
                    'options' => ''
                ]
            ],
            'styles' => [
                'background_color' => '#ffffff',
                'text_color' => '#000000',
                'button_color' => '#3b82f6',
                'button_text_color' => '#ffffff'
            ]
        ];
    }
    
    public function render(array $data): string
    {
        $defaults = static::defaults();
        
        $title = $data['title'] ?? $defaults['title'];
        $description = $data['description'] ?? $defaults['description'];
        $emailRecipient = $data['email_recipient'] ?? $defaults['email_recipient'];
        $submitText = $data['submit_text'] ?? $defaults['submit_text'];
        $successMessage = $data['success_message'] ?? $defaults['success_message'];
        $fields = $data['fields'] ?? $defaults['fields'];
        $styles = $data['styles'] ?? $defaults['styles'];
        
        $formId = 'form-' . uniqid();
        
        $fieldsHtml = '';
        foreach ($fields as $field) {
            $fieldType = $field['type'] ?? 'text';
            $fieldName = $field['name'] ?? '';
            $fieldLabel = $field['label'] ?? '';
            $fieldPlaceholder = $field['placeholder'] ?? '';
            $isRequired = $field['required'] ?? false;
            $options = $field['options'] ?? '';
            
            $requiredAttr = $isRequired ? 'required' : '';
            $fieldId = "{$formId}-{$fieldName}";
            
            $fieldHtml = match($fieldType) {
                'textarea' => "
                    <textarea name='{$fieldName}' id='{$fieldId}' 
                              placeholder='{$fieldPlaceholder}' {$requiredAttr}
                              class='w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent'
                              rows='4'></textarea>
                ",
                'select' => "
                    <select name='{$fieldName}' id='{$fieldId}' {$requiredAttr}
                            class='w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent'>
                        <option value=''>Select {$fieldLabel}</option>
                        " . $this->renderSelectOptions($options) . "
                    </select>
                ",
                'checkbox' => "
                    <div class='flex items-center'>
                        <input type='checkbox' name='{$fieldName}' id='{$fieldId}' {$requiredAttr}
                               class='w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500'>
                        <label for='{$fieldId}' class='ml-2 block text-sm text-gray-700'>
                            {$fieldLabel}
                        </label>
                    </div>
                ",
                default => "
                    <input type='{$fieldType}' name='{$fieldName}' id='{$fieldId}' 
                           placeholder='{$fieldPlaceholder}' {$requiredAttr}
                           class='w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent'>
                "
            };
            
            if ($fieldType !== 'checkbox') {
                $fieldsHtml .= "
                    <div class='mb-4'>
                        <label for='{$fieldId}' class='block text-sm font-medium text-gray-700 mb-1'>
                            {$fieldLabel}" . ($isRequired ? ' *' : '') . "
                        </label>
                        {$fieldHtml}
                    </div>
                ";
            } else {
                $fieldsHtml .= "
                    <div class='mb-4'>
                        {$fieldHtml}
                    </div>
                ";
            }
        }
        
        $buttonStyle = "background-color: {$styles['button_color']}; color: {$styles['button_text_color']};";
        
        return "
            <section class='form-section py-16' style='background-color: {$styles['background_color']}; color: {$styles['text_color']};'>
                <div class='container mx-auto px-4 max-w-2xl'>
                    " . ($title ? "<h2 class='text-3xl font-bold text-center mb-4'>{$title}</h2>" : "") . "
                    " . ($description ? "<p class='text-center mb-8'>{$description}</p>" : "") . "
                    
                    <form action='/pagebuilder/form/submit' method='POST' 
                          class='bg-white rounded-lg shadow-md p-6' id='{$formId}'>
                        @csrf
                        <input type='hidden' name='form_id' value='{$formId}'>
                        <input type='hidden' name='recipient' value='{$emailRecipient}'>
                        
                        {$fieldsHtml}
                        
                        <button type='submit' 
                                class='w-full py-3 px-6 rounded-md font-semibold hover:opacity-90 transition-opacity'
                                style='{$buttonStyle}'>
                            {$submitText}
                        </button>
                    </form>
                    
                    <div id='{$formId}-success' class='hidden mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded'>
                        {$successMessage}
                    </div>
                </div>
                
                <script>
                    document.getElementById('{$formId}').addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const formData = new FormData(e.target);
                        
                        try {
                            const response = await fetch('/pagebuilder/form/submit', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            
                            if (response.ok) {
                                e.target.style.display = 'none';
                                document.getElementById('{$formId}-success').style.display = 'block';
                            }
                        } catch (error) {
                            console.error('Form submission error:', error);
                        }
                    });
                </script>
            </section>
        ";
    }
    
    protected function renderSelectOptions(string $optionsText): string
    {
        if (empty($optionsText)) return '';
        
        $options = [];
        $lines = explode("\n", trim($optionsText));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, ':') !== false) {
                [$value, $label] = explode(':', $line, 2);
                $options[] = "<option value='" . trim($value) . "'>" . trim($label) . "</option>";
            } else {
                $options[] = "<option value='{$line}'>{$line}</option>";
            }
        }
        
        return implode('', $options);
    }
}