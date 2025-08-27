<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\JsonPageStorage;

class ExportPageCommand extends Command
{
    /**
     * A assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'pagebuilder:export {slug} {--output=}';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Exporta uma página para um arquivo JSON.';

    /**
     * O serviço de armazenamento de páginas.
     *
     * @var JsonPageStorage
     */
    protected $storage;

    /**
     * Cria uma nova instância do comando.
     *
     * @param JsonPageStorage $storage
     */
    public function __construct(JsonPageStorage $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }

    /**
     * Executa o comando do console.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $output = $this->option('output') ?? $slug . '.json';

        $page = $this->storage->find($slug);

        if (!$page) {
            $this->error("Página com o slug '{$slug}' não encontrada.");
            return 1;
        }

        $json = json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($output, $json);

        $this->info("Página '{$slug}' exportada para '{$output}' com sucesso.");
        return 0;
    }
}