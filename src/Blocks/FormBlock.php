<?php

namespace Justino\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class FormBlock implements Block
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
                'default' => 'Contact Us'
            ],
            'description' => [
                'type' => 'text',
                'label' => 'Description',
                'default' => 'Get in touch with us'
            ],
            'email_recipient' => [
                'type' => 'text',
                'label' => 'Email Recipient',
                'default' => 'admin@example.com'
            ],
            'submit_text' => [
                'type' => 'text',
                'label' => 'Submit Button Text',
                'default' => 'Send Message'
            ],
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'title' => 'Contact Us',
            'description' => 'Get in touch with us',
            'email_recipient' => 'admin@example.com',
            'submit_text' => 'Send Message',
        ];
    }
    
    public function render(array $data): string
    {
        return view('pagebuilder::blocks.form', $data)->render();
    }
}