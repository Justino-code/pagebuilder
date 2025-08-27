<?php

namespace Justino\PageBuilder\Http\Controllers;

use Justino\PageBuilder\Services\JsonPageStorage;
use Justino\PageBuilder\Services\BlockManager;
use Justino\PageBuilder\DTOs\PageData;

class PageController extends Controller
{
    /**
     * O serviço de armazenamento de páginas.
     *
     * @var JsonPageStorage
     */
    protected $storage;

    /**
     * O gerenciador de blocos.
     *
     * @var BlockManager
     */
    protected $blockManager;

    /**
     * Cria uma nova instância do controller.
     *
     * @param JsonPageStorage $storage
     * @param BlockManager $blockManager
     */
    public function __construct(JsonPageStorage $storage, BlockManager $blockManager)
    {
        $this->storage = $storage;
        $this->blockManager = $blockManager;
    }

    /**
     * Exibe uma página publicada.
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function show($slug)
    {
        $page = $this->storage->find($slug, 'page');

        if (!$page instanceof PageData || !$page->published) {
            abort(404);
        }

        $header = $this->storage->getDefault('header');
        $footer = $this->storage->getDefault('footer');

        return view('pagebuilder::page', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer,
            'blockManager' => $this->blockManager
        ]);
    }

    /**
     * Exibe uma pré-visualização de uma página (publicada ou não).
     *
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function preview($slug)
    {
        $page = $this->storage->find($slug, 'page');

        if (!$page instanceof PageData) {
            abort(404);
        }

        $header = $this->storage->getDefault('header');
        $footer = $this->storage->getDefault('footer');

        return view('pagebuilder::page', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer,
            'blockManager' => $this->blockManager
        ]);
    }
}