<?php
/**
 * Profile modal — set an avatar by remote URL (with a confirm-before-apply
 * preview) or by uploading an image. Expects $avatarSrc and $initial.
 */
$avatarSrc = $avatarSrc ?? '';
$initial = $initial ?? '?';
?>
<div id="profileModal" class="modal" x-data="profileForm('<?= e($avatarSrc) ?>', '<?= e($initial) ?>')">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close" onclick="closeProfileModal()">&times;</span>
            <h2>Your profile</h2>
        </div>
        <div class="modal-body">
            <div class="flex flex-col items-center gap-2 mb-4">
                <div class="avatar avatar-lg" style="--tw-ring-color:#06145b">
                    <template x-if="preview"><img :src="preview" alt="Avatar preview"></template>
                    <template x-if="!preview && currentSrc"><img :src="currentSrc" alt="Current avatar"></template>
                    <template x-if="!preview && !currentSrc"><span x-text="initial"></span></template>
                </div>
                <p class="text-xs text-tp-black/60" x-text="preview ? 'Preview — apply to save' : 'Current avatar'"></p>
            </div>

            <div class="field-row">
                <label class="field-label" for="avatar_url_input">Image URL</label>
                <div class="flex gap-2">
                    <input type="url" id="avatar_url_input" x-model="url" placeholder="https://example.com/me.png"
                        style="flex:1" @keydown.enter.prevent="previewUrl()">
                    <button type="button" class="btn-white btn-sm" @click="previewUrl()">Preview</button>
                </div>
            </div>
            <button type="button" class="btn-primary w-full" x-show="preview" x-cloak @click="applyUrl()">
                <i class="fas fa-check"></i>&nbsp;Apply this image
            </button>

            <div class="flex items-center gap-3 my-4 text-tp-black/40 text-xs">
                <span class="flex-1 border-t border-tp-black/20"></span>OR<span class="flex-1 border-t border-tp-black/20"></span>
            </div>

            <div class="field-row">
                <label class="field-label" for="avatar_file_input">Upload an image</label>
                <input type="file" id="avatar_file_input" accept="image/png,image/jpeg,image/gif,image/webp"
                    @change="uploadFile($event)">
                <p class="text-xs text-tp-black/50 mt-1">JPEG, PNG, GIF or WebP · max 2 MB</p>
            </div>
        </div>
        <div class="modal-footer">
            <div class="select-button">
                <button type="button" class="btn-danger btn-sm" x-show="currentSrc" x-cloak @click="removeAvatar()">
                    <i class="fas fa-trash"></i>&nbsp;Remove avatar
                </button>
                <button type="button" class="btn-white btn-sm" onclick="closeProfileModal()">Close</button>
            </div>
        </div>
    </div>
</div>
