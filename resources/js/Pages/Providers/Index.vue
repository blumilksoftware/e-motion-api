<script setup>
import Provider from '../../Shared/Components/Provider.vue'
import {useForm, usePage, router} from '@inertiajs/vue3'
import {computed, ref, watch} from 'vue'
import AdminNavigation from '@/Shared/Layout/AdminNavigation.vue'
import {XMarkIcon, MagnifyingGlassIcon, ChevronDownIcon} from '@heroicons/vue/24/outline'
import ErrorMessage from '@/Shared/Components/ErrorMessage.vue'
import {onClickOutside} from '@vueuse/core'
import {debounce} from 'lodash/function'
import Pagination from '@/Shared/Components/Pagination.vue'
import PaginationInfo from '@/Shared/Components/PaginationInfo.vue'
import PrimarySaveButton from '@/Shared/Components/PrimarySaveButton.vue'
import {useToast} from 'vue-toastification'
import {__} from '@/translate'
import axios from 'axios'

const page = usePage()
const toast = useToast()

const props = defineProps({
  providers: Object,
})

const commaInputError = ref('')

function storeProvider() {
  commaInputError.value = ''
  storeProviderForm.post('/admin/providers', {
    onSuccess: () => {
      storeProviderForm.reset()
      toggleStoreDialog()
      toast.success(__('Provider created successfully.'))
    },
    onError: (errors) => {
      storeErrors.value = errors
      toast.error(__('There was an error creating the provider!'))
    },
  })
}

const storeProviderForm = useForm({
  name: '',
  url: '',
  color: '',
})

const isStoreDialogOpened = ref(false)
const storeDialog = ref(null)
onClickOutside(storeDialog, () => (isStoreDialogOpened.value = false))

function toggleStoreDialog() {
  isStoreDialogOpened.value = !isStoreDialogOpened.value
}

const searchInput = ref('')


watch(searchInput, debounce(() => {
  router.get(`/admin/providers?search=${searchInput.value}`, {}, {
    preserveState: true,
    replace: true,
  })
}, 300), {deep: true})

function clearInput() {
  searchInput.value = ''
}

const sortingOptions = [
  {name: 'Latest', href: '/admin/providers?order=latest'},
  {name: 'Oldest', href: '/admin/providers?order=oldest'},
  {name: 'By name', href: '/admin/providers?order=name'},
]

const isSortDialogOpened = ref(false)
const sortDialog = ref(null)
onClickOutside(sortDialog, () => (isSortDialogOpened.value = false))

function toggleSortDialog() {
  isSortDialogOpened.value = !isSortDialogOpened.value
}

const formattedColor = computed({
      get() {
        return storeProviderForm.color
      },
      set: function (colorValue) {
        colorValue = colorValue.startsWith('#') ? colorValue : `#${colorValue}`
        storeProviderForm.color = colorValue

        if (colorValue.length === 7) {
          storeProviderForm.errors.color = null
        } else {
          storeProviderForm.errors.color = 'Color must be 6 characters long.'
        }
      },
    },
)

function uploadImage(event) {
  let image = event.target.files[0];
  let formData = new FormData();
  const imageName = storeProviderForm.name.toLowerCase() + '.png'

  formData.append('image', image);

  axios.post(`/image/upload/${imageName}`, formData)
      .then(response => {
        if (response.status === 200 && response.data.success) {
          toast.success(__('Image added successfully.'));
        } else {
          toast.error(__('Image should be: \n • 64px per 64 px \n • max 40 kb \n • .png')); //
        }
      })
      .catch(error => {
        toast.error(__('Something went wrong on our side. Try again later.'));
      });
}
</script>

<template>
  <div class="flex h-full min-h-screen flex-col md:flex-row">
    <AdminNavigation :url="page.url"/>

    <div class="flex w-full md:justify-end">
      <div class="mt-16 flex h-full w-full flex-col justify-between md:mt-0 md:w-2/3 lg:w-3/4 xl:w-5/6">
        <div class="m-4 flex flex-col lg:mx-8">
          <div v-if="isStoreDialogOpened" class="fixed inset-0 z-50 flex items-center bg-black/50">
            <div ref="storeDialog"
                 class="mx-auto w-11/12 rounded-lg bg-white shadow-lg sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3">
              <div class="flex w-full justify-end">
                <button class="px-4 pt-4" @click="toggleStoreDialog">
                  <XMarkIcon class="h-6 w-6"/>
                </button>
              </div>

              <div class="flex flex-col p-6 pt-0">
                <h1 class="mb-3 text-lg font-bold text-gray-800">
                  {{ __('Create provider') }}
                </h1>

                <form class="flex flex-col text-xs font-bold text-gray-600" @submit.prevent="storeProvider">
                  <label class="mb-1 mt-4">{{ __('Name') }}</label>
                  <input v-model="storeProviderForm.name"
                         class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 md:p-3"
                         type="text" required>
                  <ErrorMessage :message="storeProviderForm.errors.name"/>

                  <label class="mb-1 mt-4">{{ __('Url') }}</label>
                  <input v-model="storeProviderForm.url"
                         class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                         type="text"
                  >
                  <ErrorMessage :message="storeProviderForm.errors.url"/>

                  <label class="mb-1 mt-4">{{ __('Color') }}</label>
                  <input v-model="formattedColor"
                         class="rounded-md border border-blumilk-100 p-4 text-sm font-semibold text-gray-800 shadow md:p-3"
                         type="text"
                  >
                  <ErrorMessage :message="storeProviderForm.errors.color"/>

                  <label class="mb-1 mt-4">{{ __('Image') }}</label>
                  <input type="file" accept="image/png" class="mb-2" @change="uploadImage" required>

                  <div class="flex w-full justify-end">
                    <PrimarySaveButton>
                      {{ __('Save') }}
                    </PrimarySaveButton>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="mb-3 mt-4 flex flex-wrap items-center justify-end md:justify-between">
            <button
                class="mr-1 rounded bg-blumilk-500 px-5 py-3 text-sm font-medium text-white shadow-md hover:bg-blumilk-400 md:py-2"
                @click="toggleStoreDialog"
            >
              {{ __('Create provider') }}
            </button>

            <div class="m-1 flex w-full rounded-md shadow-sm md:w-fit">
              <div class="relative flex grow items-stretch focus-within:z-10">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                  <MagnifyingGlassIcon class="h-5 w-5 text-gray-800"/>
                </div>
                <input v-model.trim="searchInput" type="text"
                       class="block w-full rounded border-0 py-3 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-sm placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blumilk-300 sm:text-sm sm:leading-6 md:py-1.5"
                       :placeholder="__('Search provider')"
                >
              </div>
              <button v-if="searchInput.length" type="button"
                      class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-800 ring-1 ring-inset ring-gray-300 hover:bg-blumilk-25"
                      @click="clearInput"
              >
                <XMarkIcon class="h-5 w-5"/>
              </button>
            </div>
          </div>

          <div class="flex w-full flex-wrap items-center justify-between">
            <div v-if="props.providers.data.length" class="w-1/2">
              <PaginationInfo :meta="props.providers.meta"/>
            </div>

            <div class="relative inline-block text-left">
              <div>
                <button ref="sortDialog"
                        class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                        aria-expanded="false" aria-haspopup="true" @click="toggleSortDialog"
                >
                  {{ __('Sort') }}
                  <ChevronDownIcon class="ml-1 h-5 w-5"/>
                </button>
              </div>

              <div v-if="isSortDialogOpened"
                   class="absolute right-1 z-10 mt-3.5 w-max rounded-md bg-white shadow-lg shadow-gray-300 ring-1 ring-gray-300 focus:outline-none"
                   role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
              >
                <div class="py-1" role="none">
                  <InertiaLink v-for="option in sortingOptions" :key="option.href"
                               :href="option.href" class="block px-4 py-2 text-sm text-gray-500 hover:text-blumilk-400"
                               role="menuitem" tabindex="-1"
                  >
                    <span
                        :class="{'font-medium text-blumilk-400': page.url.startsWith(option.href) || ((page.url === '/admin/providers' || page.url.startsWith('/admin/providers?search=') || page.url.startsWith('/admin/providers?page=')) && option.href.startsWith('/admin/providers?order=latest'))}"
                    >
                      {{ __(option.name) }}
                    </span>
                  </InertiaLink>
                </div>
              </div>
            </div>
          </div>

          <div v-if="props.providers.data.length" class="rounded-lg ring-gray-300 sm:ring-1">
            <table class="min-w-full">
              <thead>
              <tr>
                <th scope="col"
                    class="py-3.5 pl-5 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:table-cell"
                >
                  {{ __('Name') }}
                </th>
                <th scope="col" class="hidden py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">
                  {{ __('Url') }}
                </th>
                <th scope="col" class="hidden py-3.5 text-left text-sm font-semibold text-gray-900 lg:table-cell">
                  {{ __('Color') }}
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="provider in props.providers.data" :key="provider.id" class="border-t">
                <Provider :provider="provider"/>
              </tr>
              </tbody>
            </table>
          </div>

          <div v-else>
            <p class="mt-6 text-lg font-medium text-gray-500">
              {{ __('Sorry we couldn`t find any providers.') }}
            </p>
          </div>

          <Pagination :meta="props.providers.meta" :links="props.providers.links"/>
        </div>
      </div>
    </div>
  </div>
</template>