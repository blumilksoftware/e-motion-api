<script setup>
import { computed, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { PencilIcon, TrashIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'
import { onClickOutside } from '@vueuse/core'
import SecondarySaveButton from '@/Shared/Components/SecondarySaveButton.vue'
import { useToast } from 'vue-toastification'
import { __ } from '@/translate'
import UploadFileButton from './UploadFileButton.vue'
import DeleteModal from '@/Shared/Components/DeleteModal.vue'

const showDeleteModal = ref(false)
const toast = useToast()
const props = defineProps({
  provider: Object,
})

function destroyProvider(providerName) {
  router.delete(`/admin/providers/${providerName}`)

  toast.success(__('Provider deleted successfully.'))
  showDeleteModal.value = false
}

const formattedColor = computed({
  get() {
    return updateProviderForm.color
  },
  set: function (colorValue) {
    updateProviderForm.color = colorValue.startsWith('#') ? colorValue : `#${colorValue}`
  },
},
)

const formattedName = computed({
  get() {
    return updateProviderForm.name
  },
  set: function (nameValue) {
    nameValue = nameValue.charAt(0).toUpperCase() + nameValue.slice(1)
    updateProviderForm.name = nameValue
  },
},
)

function updateProvider(providerName) {
  console.log(providerName)
  updateProviderForm.put(`/admin/providers/${providerName}`, {
    onSuccess: () => {
      toggleEditDialog()
      toast.success(__('Provider updated successfully.'))
    },
    onError: () => {
      console.log(updateProviderForm)
      toast.error(__('There was an error updating the provider.'))
    },
  })
}

const updateProviderForm = useForm({
  name: props.provider.name,
  url: props.provider.url,
  color: props.provider.color,
  file: props.provider.file,
})

const isEditDialogOpened = ref(false)
const editDialog = ref(null)
onClickOutside(editDialog, () => (isEditDialogOpened.value = false))

function toggleEditDialog() {
  isEditDialogOpened.value = !isEditDialogOpened.value
}

function goToWebsite(url) {
  if (!url.startsWith('http://') && !url.startsWith('https://')) {
    url = 'https://' + url
  }
  window.open(url, '_blank')
}
</script>

<template>
  <td class="py-4 pl-4 text-sm sm:pl-6 sm:pr-3">
    <div class="flex items-center font-medium text-gray-800">
      <div class="mr-2 flex h-8 w-fit shrink-0 items-center justify-center rounded-md p-1"
           :style="{ 'background-color': provider.color }"
      >
        <img class="w-8" :src="'/providers/' + provider.name.toLowerCase() + '.png'" alt="">
      </div>
      <div>
        {{ provider.name }}
      </div>
    </div>
  </td>
  <td class="hidden break-all py-3.5 text-sm text-gray-500 lg:table-cell">
    <p v-if="provider.url" class="cursor-pointer break-all rounded text-blumilk-500"
       @click="goToWebsite(provider.url)"
    >
      {{ provider.url }}
    </p>
    <p v-else class="break-all rounded">
      -
    </p>
  </td>
  <td class="hidden break-all py-3.5 text-sm text-gray-500 lg:table-cell">
    {{ provider.color }}
  </td>

  <td class="relative table-cell justify-end border-t text-right text-xs font-medium sm:pl-3 md:pr-2">
    <span class="flex flex-wrap">
      <div
        class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-gray-200"
      >
        <PencilIcon class="h-5 w-8 text-gray-200" />
        {{ __('Edit') }}
      </div>

      <button class="mx-0.5 mb-1 flex w-fit shrink-0 items-center rounded py-1 pr-2 text-rose-500 hover:bg-rose-100"
              @click="showDeleteModal = true"
      >
        <TrashIcon class="h-5 w-8 text-rose-500" />
        {{ __('Delete') }}
      </button>

      <DeleteModal v-if="showDeleteModal" :name="provider.name" :type="'Provider'" @close="showDeleteModal = false" @delete="destroyProvider(provider.name)" />
    </span>
  </td>

  <div v-if="isEditDialogOpened" class="flex flex-col overflow-y-auto">
    <div class="fixed inset-0 z-10 flex items-center overflow-y-auto bg-black/50">
      <div ref="editDialog" class="mx-auto w-11/12 rounded-lg bg-white pb-6 sm:w-5/6 md:w-3/4 lg:w-1/2 xl:w-1/3">
        <div class="flex w-full justify-end">
          <button class="px-4 pt-4" @click="toggleEditDialog">
            <XMarkIcon class="size-6" />
          </button>
        </div>

        <form class="flex flex-col rounded px-6 text-xs font-bold text-gray-600"
              @submit.prevent="updateProvider(provider.name)"
        >
          <h1 class="mb-3 text-lg font-bold text-gray-800">
            {{ __('Update provider') }}
          </h1>
          <label class="mb-1 mt-4">{{ __('Name') }}</label>
          <input v-model="formattedName"
                 class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                 type="text"
          >
          <ErrorMessage :message="updateProviderForm.errors.name" />
          <label class="mb-1 mt-4">{{ __('Url') }}</label>
          <input v-model="updateProviderForm.url"
                 class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                 type="text"
          >
          <ErrorMessage :message="updateProviderForm.errors.url" />
          <label class="mb-1 mt-4">{{ __('Color') }}</label>
          <input v-model="formattedColor"
                 class="rounded border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                 type="text"
          >
          <ErrorMessage :message="updateProviderForm.errors.color" />

          <label class="mb-1 mt-4">{{ __('Logo') }}</label>
          <UploadFileButton type="file" accept="image/png"
                            @input="updateProviderForm.file = $event.target.files[0]"
          />
          <ErrorMessage :message="updateProviderForm.errors.file" />

          <div class="flex w-full justify-end">
            <SecondarySaveButton>
              {{ __('Save') }}
            </SecondarySaveButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
