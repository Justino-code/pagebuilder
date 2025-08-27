<div class="page-manager">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @if(count($pages) > 0)
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">{{ __('pagebuilder::messages.title') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('pagebuilder::messages.slug') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('pagebuilder::messages.status') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('pagebuilder::messages.created_at') }}</th>
                        <th class="px-4 py-2 text-left">{{ __('pagebuilder::messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php dd(pagebuilder); @endphp
                    @foreach($pages as $page)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $page['title'] }}</td> <!-- CORRETO: sintaxe de array -->
                            <td class="px-4 py-3">{{ $page['slug'] }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded {{ $page['published'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $page['published'] ? __('pagebuilder::messages.published') : __('pagebuilder::messages.draft') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($page['createdAt'])->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex space-x-2">
                                    <button 
                                        wire:click="editPage('{{ $page['slug'] }}')"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm"
                                    >
                                        {{ __('pagebuilder::messages.edit') }}
                                    </button>
                                    
                                    @if($page['published'])
                                        <a 
                                            href="{{ route('pagebuilder.page.show', $page['slug']) }}"
                                            target="_blank"
                                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm"
                                        >
                                            {{ __('pagebuilder::messages.view') }}
                                        </a>
                                    @endif
                                    
                                    <button 
                                        wire:click="togglePublish('{{ $page['slug'] }}')"
                                        class="px-3 py-1 {{ $page['published'] ? 'bg-yellow-500' : 'bg-green-500' }} text-white rounded hover:{{ $page['published'] ? 'bg-yellow-600' : 'bg-green-600' }} text-sm"
                                    >
                                        {{ $page['published'] ? __('pagebuilder::messages.unpublish') : __('pagebuilder::messages.publish') }}
                                    </button>
                                    
                                    <button 
                                        wire:click="confirmDelete('{{ $page['slug'] }}')"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                                    >
                                        {{ __('pagebuilder::messages.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-4 py-8 text-center text-gray-500">
                <div class="text-4xl mb-4">📄</div>
                <p>{{ __('pagebuilder::messages.no_pages_found') }}</p>
                <a href="{{ route('pagebuilder.pages.create') }}" class="text-blue-500 hover:underline mt-2 inline-block">
                    {{ __('pagebuilder::messages.create_first_page') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">{{ __('pagebuilder::messages.confirm_delete') }}</h3>
                <p class="mb-4">{{ __('pagebuilder::messages.confirm_delete_page') }}</p>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors"
                    >
                        {{ __('pagebuilder::messages.cancel') }}
                    </button>
                    <button 
                        wire:click="deletePage"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
                    >
                        {{ __('pagebuilder::messages.delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mensagem de Feedback -->
    @if(session()->has('message'))
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('message') }}
            </div>
        </div>
        
        <script>
            // Auto-hide da mensagem após 3 segundos
            setTimeout(() => {
                const message = document.querySelector('.fixed.top-4.right-4');
                if (message) {
                    message.remove();
                }
            }, 3000);
        </script>
    @endif
</div>