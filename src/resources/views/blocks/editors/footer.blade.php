<div class="footer-editor space-y-6">
    <!-- Template Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
            <input type="text" wire:model="block.data.name" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="Footer name" required>
        </div>
        <div class="flex items-center">
            <input type="checkbox" 
                   wire:model="block.data.is_default"
                   id="is-default-footer"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
            <label for="is-default-footer" class="ml-2 text-sm text-gray-700">
                Set as default footer
            </label>
        </div>
    </div>

    <!-- Logo Settings -->
    <div class="p-4 bg-gray-50 rounded-lg">
        <h4 class="font-medium text-gray-800 mb-3">Logo Settings</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Type</label>
                <select wire:model="block.data.logo.type" class="w-full p-2 border border-gray-300 rounded-md">
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo Link</label>
                <input type="text" wire:model="block.data.logo.link" 
                       class="w-full p-2 border border-gray-300 rounded-md" 
                       placeholder="/">
            </div>
        </div>

        <!-- Text Logo -->
        @if(($block['data']['logo']['type'] ?? 'text') === 'text')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Text *</label>
            <input type="text" wire:model="block.data.logo.text" 
                   class="w-full p-2 border border-gray-300 rounded-md" 
                   placeholder="My Website" required>
        </div>
        @else
        <!-- Image Logo -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Image *</label>
            <div class="flex space-x-2">
                <input type="text" wire:model="block.data.logo.image" 
                       class="flex-1 p-2 border border-gray-300 rounded-md" 
                       placeholder="Image URL" required>
                <button type="button" 
                        wire:click="$emit('openMediaLibrary', 'logo.image')"
                        class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    📁 Select
                </button>
            </div>
            @if($block['data']['logo']['image'])
            <div class="mt-2">
                <img src="{{ $block['data']['logo']['image'] }}" 
                     alt="Logo preview" 
                     class="w-32 h-16 object-contain">
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Footer Sections -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Footer Sections</label>
        
        <div class="space-y-4">
            @foreach($block['data']['sections'] as $sectionIndex => $section)
            <div class="section-item bg-gray-50 p-4 rounded-lg border">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium">Section #{{ $sectionIndex + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('sections', {{ $sectionIndex }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <!-- Section Title -->
                <div class="mb-4">
                    <label class="block text-sm text-gray-600 mb-1">Section Title *</label>
                    <input type="text" wire:model="block.data.sections.{{ $sectionIndex }}.title" 
                           class="w-full p-2 border border-gray-300 rounded-md" 
                           placeholder="Section title" required>
                </div>

                <!-- Section Links -->
                <div>
                    <label class="block text-sm text-gray-600 mb-2">Links</label>
                    
                    <div class="space-y-3">
                        @foreach($section['links'] as $linkIndex => $link)
                        <div class="link-item bg-white p-3 rounded border">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="text-sm font-medium">Link #{{ $linkIndex + 1 }}</h5>
                                <button type="button" 
                                        wire:click="removeRepeaterItem('sections.{{ $sectionIndex }}.links', {{ $linkIndex }})"
                                        class="text-red-500 hover:text-red-700 text-xs">
                                    Remove
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Label *</label>
                                    <input type="text" 
                                           wire:model="block.data.sections.{{ $sectionIndex }}.links.{{ $linkIndex }}.label" 
                                           class="w-full p-1 border border-gray-300 rounded text-sm" 
                                           placeholder="Link label" required>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">URL *</label>
                                    <input type="text" 
                                           wire:model="block.data.sections.{{ $sectionIndex }}.links.{{ $linkIndex }}.url" 
                                           class="w-full p-1 border border-gray-300 rounded text-sm" 
                                           placeholder="/page" required>
                                </div>
                            </div>

                            <div class="mt-2">
                                <label class="block text-xs text-gray-500 mb-1">Target</label>
                                <select wire:model="block.data.sections.{{ $sectionIndex }}.links.{{ $linkIndex }}.target" 
                                        class="w-full p-1 border border-gray-300 rounded text-sm">
                                    <option value="_self">Same Tab</option>
                                    <option value="_blank">New Tab</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" 
                            wire:click="addRepeaterItem('sections.{{ $sectionIndex }}.links')"
                            class="mt-2 px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">
                        + Add Link
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" 
                wire:click="addRepeaterItem('sections')"
                class="mt-3 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            + Add Section
        </button>
    </div>

    <!-- Social Links -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
        
        <div class="space-y-3">
            @foreach($block['data']['social_links'] as $socialIndex => $social)
            <div class="social-item bg-gray-50 p-3 rounded-lg border">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Social #{{ $socialIndex + 1 }}</h4>
                    <button type="button" 
                            wire:click="removeRepeaterItem('social_links', {{ $socialIndex }})"
                            class="text-red-600 hover:text-red-800 text-sm">
                        Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Platform</label>
                        <select wire:model="block.data.social_links.{{ $socialIndex }}.platform" 
                                class="w-full p-2 border border-gray-300 rounded-md">
                            <option value="facebook">Facebook</option>
                            <option value="twitter">Twitter</option>
                            <option value="instagram">Instagram</option>
                            <option value="linkedin">LinkedIn</option>
                            <option value="youtube">YouTube</option>
                            <option value="tiktok">TikTok</option>
                            <option value="github">GitHub</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">URL *</label>
                        <input type="text" wire:model="block.data.social_links.{{ $socialIndex }}.url" 
                               class="w-full p-2 border border-gray-300 rounded-md" 
                               placeholder="https://..." required>
                    </div>
                </div>

                @if(($block['data']['social_links'][$socialIndex]['platform'] ?? '') === 'custom')
                <div class="mt-3">
                    <label class="block text-sm text-gray-600 mb-1">Custom Icon</label>
                    <input type="text" wire:model="block.data.social_links.{{ $socialIndex }}.icon" 
                           class="w-full p-2 border border-gray-300 rounded-md" 
                           placeholder="🔗 or emoji">
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <button type="button" 
                wire:click="addRepeaterItem('social_links')"
                class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            + Add Social Link
        </button>
    </div>

    <!-- Copyright -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Copyright Text</label>
        <textarea wire:model="block.data.copyright" 
                  rows="2"
                  class="w-full p-2 border border-gray-300 rounded-md" 
                  placeholder="Copyright text"></textarea>
    </div>

    <!-- Footer Styles -->
    <div class="p-4 bg-blue-50 rounded-lg">
        <h4 class="font-medium text-blue-800 mb-3">Footer Styles</h4>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Background Color</label>
                <input type="color" wire:model="block.data.styles.background_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Text Color</label>
                <input type="color" wire:model="block.data.styles.text_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Link Color</label>
                <input type="color" wire:model="block.data.styles.link_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Hover Color</label>
                <input type="color" wire:model="block.data.styles.hover_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Border Color</label>
                <input type="color" wire:model="block.data.styles.border_color" 
                       class="w-full h-10 p-1 border border-gray-300 rounded-md">
            </div>
        </div>
    </div>
</div>