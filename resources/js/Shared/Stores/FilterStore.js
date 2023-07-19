import { defineStore } from 'pinia'

export const useFilterStore = defineStore('useFilterStore', {
  state: () => ({
    selectedCity: null,

    selectedCountryId: null,
    selectedProviderId: null,
  }),
  actions: {
    changeSelectedCity(city) {
      this.selectedCity = city
    },
    changeSelectedCountry(countryId) {
      this.selectedCountryId = countryId
    },
    changeSelectedProvider(providerId) {
      this.selectedProviderId = providerId
    },
  },
})
