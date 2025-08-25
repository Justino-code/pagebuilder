# Page Builder para Laravel

Um construtor de páginas completo com interface drag-and-drop para Laravel, integrado com Livewire, com headers e footers editáveis.

## 🚀 Características

- 🎨 Editor visual drag-and-drop
- ⚡ Integração com Livewire v3
- 📁 Armazenamento em JSON (sem banco de dados necessário)
- 🖼️ Biblioteca de mídia com gerenciamento de imagens
- 🧩 Sistema modular de blocos
- 🌐 Suporte a multi-idiomas
- 🎯 Headers e footers editáveis
- 💾 Funcionalidade de exportar/importar
- 🔒 Autenticação baseada em roles
- 📊 Logs de atividade
- 🎨 Suporte a TailwindCSS e Bootstrap

## 📦 Instalação

1. Instale o pacote via Composer:
```bash
composer require justino/page-builder
```

2. Execute o comando de instalação:
```bash
php artisan pagebuilder:install
```

3. Configure o arquivo `config/pagebuilder.php` conforme necessário.

## 🎯 Configuração Rápida

Após a instalação, adicione o middleware de autenticação ao seu `boostrap/app.php`:

```php
protected $routeMiddleware = [
    // ...
    'pagebuilder.auth' => \Justino\PageBuilder\Http\Middleware\PageBuilderAuth::class,
];
```

## 📖 Utilização Básica

### Acessando o Page Builder
Visite `/page-builder` (ou a rota configurada) para acessar a interface do construtor.

### Criando Páginas
1. Clique em "Criar Nova Página"
2. Adicione blocos usando a sidebar
3. Customize o conteúdo e estilos de cada bloco
4. Salve e publique sua página

### Gerando Templates
Headers e footers são gerenciados como tipos especiais de blocos:
```bash
# Ver templates de header
/page-builder/templates/header

# Ver templates de footer  
/page-builder/templates/footer
```

### Usando nas Views Blade
```php
// Exibir uma página pelo slug
{{ \Justino\PageBuilder\Facades\PageBuilder::renderPage('sobre-nos') }}

// Obter conteúdo da página como array
{{ \Justino\PageBuilder\Facades\PageBuilder::getPage('contato') }}
```

## 🧩 Blocos Disponíveis

| Bloco | Ícone | Descrição |
|-------|-------|-----------|
| Hero | 📱 | Seção principal com título e call-to-action |
| Texto | 📝 | Conteúdo de texto formatado |
| CTA | 📢 | Chamada para ação com botão |
| Cards | 🃏 | Grade de cards com ícones |
| Galeria | 🖼️ | Galeria de imagens |
| Formulário | 📋 | Formulário de contato |
| Header | 🔝 | Template de cabeçalho |
| Footer | 🔻 | Template de rodapé |

## ⚙️ Configuração

### Arquivo de Configuração (`config/pagebuilder.php`)

```php
return [
    'storage' => [
        'driver' => 'json', // ou 'database'
        'path' => storage_path('app/pagebuilder'),
    ],
    
    'media' => [
        'disk' => 'public',
        'path' => 'pagebuilder/media',
        'max_file_size' => 2048, // KB
    ],
    
    'route' => [
        'prefix' => 'page-builder',
        'middleware' => ['web', 'pagebuilder.auth'],
    ],
    
    'auth' => [
        'enabled' => true,
        'roles' => ['admin'], // roles permitidas
    ],
    
    'ui' => [
        'css_framework' => 'tailwind', // ou 'bootstrap'
    ],
];
```

### Variáveis de Ambiente (.env)

```env
PAGEBUILDER_STORAGE_DRIVER=json
PAGEBUILDER_MEDIA_DISK=public
PAGEBUILDER_ROUTE_PREFIX=page-builder
PAGEBUILDER_AUTH_ROLES=admin,editor
PAGEBUILDER_CSS_FRAMEWORK=tailwind
```

## 🔧 Comandos Artisan

```bash
# Instalar o page builder
php artisan pagebuilder:install

# Exportar uma página
php artisan pagebuilder:export sobre-nos --output=sobre.json

# Importar uma página
php artisan pagebuilder:import sobre.json

# Listar todas as páginas
php artisan pagebuilder:list
```

## 🛠️ Criando Blocos Personalizados

Crie blocos personalizados implementando a interface `Block`:

```php
<?php

namespace App\PageBuilder\Blocks;

use Justino\PageBuilder\Contracts\Block;

class JustinoBlocoPersonalizado implements Block
{
    public static function type(): string
    {
        return 'Meu-bloco';
    }
    
    public static function label(): string
    {
        return 'Meu Bloco Personalizado';
    }
    
    public static function icon(): string
    {
        return '⭐';
    }
    
    public static function schema(): array
    {
        return [
            'titulo' => [
                'type' => 'text',
                'label' => 'Título',
                'default' => 'Título Padrão'
            ],
            'conteudo' => [
                'type' => 'richtext',
                'label' => 'Conteúdo',
                'default' => '<p>Conteúdo padrão</p>'
            ]
        ];
    }
    
    public static function defaults(): array
    {
        return [
            'titulo' => 'Título Padrão',
            'conteudo' => '<p>Conteúdo padrão</p>'
        ];
    }
    
    public function render(array $data): string
    {
        return view('blocks.meu-bloco', $data)->render();
    }
}
```

Registre seu bloco em `config/pagebuilder.php`:

```php
'blocks' => [
    'default' => [
        // ... blocos existentes
        \App\PageBuilder\Blocks\JustinoBlocoPersonalizado::class,
    ],
],
```

## 🌐 Tradução

### Adicionando Novos Idiomas

Crie arquivos de tradução em `resources/lang/{idioma}/messages.php`:

```php
// resources/lang/pt/messages.php
return [
    'page_saved' => 'Página salva com sucesso.',
    'blocks' => [
        'hero' => 'Seção Hero',
        'text' => 'Conteúdo de Texto',
    ],
    // ...
];
```

### Usando Traduções

```php
// Em views Blade
{{ __('pagebuilder::messages.page_saved') }}

// Em componentes Livewire
$this->emit('message', __('pagebuilder::messages.template_saved'));
```

## 🔒 Segurança

### Controle de Acesso

Configure as roles permitidas no arquivo de configuração:

```php
'auth' => [
    'enabled' => true,
    'roles' => ['admin', 'editor'], // Roles com acesso
],
```

### Middleware Personalizado

Crie middleware personalizado para controle de acesso:

```php
<?php

namespace App\Http\Middleware;

use Closure;

class CustomPageBuilderAuth
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->can('manage-pages')) {
            abort(403, 'Acesso não autorizado.');
        }
        
        return $next($request);
    }
}
```

## 📊 Logs e Monitoramento

Os logs são armazenados no canal configurado:

```php
'logging' => [
    'enabled' => true,
    'channel' => 'stack',
],
```

Exemplo de entrada de log:
```
[YYYY-MM-DD HH:MM:SS] local.INFO: Page edited: about-us by user 1
```

## 🚀 Deploy e Produção

### Otimizações

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache
```

### Backup de Páginas

```bash
# Exportar todas as páginas
php artisan pagebuilder:export-all

# Fazer backup do diretório de armazenamento
tar -czf pagebuilder-backup.tar.gz storage/app/pagebuilder/
```

## 🤝 Contribuição

1. Faça o fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este pacote é software open-source licenciado sob a [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Suporte

- 📧 Email: jkotingo@gmail.com
- 🐛 Issues: [GitHub Issues](https://github.com/justino-code/page-builder/issues)

## 🔄 Atualizações

### Versão 1.0.0
- ✅ Editor visual drag-and-drop
- ✅ Sistema modular de blocos
- ✅ Headers e footers editáveis
- ✅ Armazenamento em JSON
- ✅ Suporte a multi-idiomas

### Próximas Versões
- 🔄 Integração com banco de dados
- 🔄 Blocos de e-commerce
- 🔄 Analytics integrado
- 🔄 Versionamento de páginas

---

**Nota**: Este pacote está em desenvolvimento ativo. Recomendamos testar em ambiente de desenvolvimento antes de usar em produção.

## 🤖 Nota de Desenvolvimento

*Este projeto foi desenvolvido com suporte de ferramentas de Inteligência Artificial para auxiliar na geração de código, documentação e otimização de processos de desenvolvimento.*