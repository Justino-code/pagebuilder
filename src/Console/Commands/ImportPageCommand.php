<?php

namespace Justino\PageBuilder\Console\Commands;

use Illuminate\Console\Command;
use Justino\PageBuilder\Services\JsonPageStorage;

class ImportPageCommand extends Command
{
    /**
     * A assinatura do comando no console.
     *
     * @var string
     */
    protected $signature = 'pagebuilder:import {file}';

    /**
     * A descrição do comando no console.
     *
     * @var string
     */
    protected $description = 'Importa uma página a partir de um arquivo JSON.';

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
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Arquivo '{$file}' não encontrado.");
            return 1;
        }

        $content = file_get_contents($file);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Arquivo JSON inválido: ' . json_last_error_msg());
            return 1;
        }

        $result = $this->storage->save($data);

        if ($result) {
            $slug = $data['slug'] ?? 'desconhecido';
            $this->info("Página '{$slug}' importada com sucesso.");
            return 0;
        }

        $this->error('Falha ao importar a página.');
        return 1;
    }
}