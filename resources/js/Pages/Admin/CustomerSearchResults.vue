<template>
    <Head title="Customer Search" />

    <AdminLayout>
      <div class="container mx-auto px-4 py-8 transition-all duration-300 ease-in-out">
        <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-300 ease-in-out transform hover:scale-[1.02]">
          <div class="p-6 md:p-8">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200 transition-colors duration-300">Customer Search</h1>

            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 mb-6">
              <select
                v-model="searchCriteria"
                class="flex-1 p-3 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-300"
              >
                <option value="phone_number">Phone Number</option>
                <option value="nid_number">NID Number</option>
                <option value="name">Name</option>
                <option value="name_bn">Name (Bangla)</option>
                <option value="father_name">Father's Name</option>
                <option value="mother_name">Mother's Name</option>
              </select>

              <input
                v-model="searchQuery"
                @input="debounceSearch"
                type="text"
                :placeholder="`Search by ${searchCriteria.replace('_', ' ')}`"
                class="flex-1 p-3 border rounded-md bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all duration-300"
              >
            </div>

            <transition name="fade" mode="out-in">
                <div v-if="loading" class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Searching...</p>
                </div>

                <div v-else-if="customers.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="customer in customers"
                        :key="customer.id"
                        class="card p-4 rounded-lg shadow-lg transition-all duration-300 ease-in-out"
                    >
                        <Link
                        :href="route('admin.customers.show', customer.id)"
                        class="flex flex-col h-full"
                        >
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold mb-2 text-gray-800 dark:text-gray-200">{{ customer.name }}</h2>
                            <p v-if="customer.name_bn" class="mb-2 text-gray-600 dark:text-gray-400">{{ customer.name_bn }}</p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>NID:</strong> {{ customer.nid_number }}</p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Phone:</strong> {{ customer.phone_number }}</p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Father's Name:</strong> {{ customer.father_name }}</p>
                            <p class="text-gray-700 dark:text-gray-300"><strong>Mother's Name:</strong> {{ customer.mother_name }}</p>
                        </div>
                        </Link>
                    </div>
                </div>

                <div v-else-if="searchQuery" class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-400">No customers found.</p>
                </div>
            </transition>

          </div>
        </div>
      </div>
    </AdminLayout>
  </template>

  <script>
  import { ref, watch } from 'vue'
  import { Head, Link } from '@inertiajs/vue3'
  import { router } from '@inertiajs/vue3'
  import debounce from 'lodash/debounce'
  import AdminLayout from '@/Layouts/AdminLayout.vue'

  export default {
    components: {
      Head,
      AdminLayout,
      Link, // Register Link component here
    },
    setup() {
      const searchCriteria = ref('phone_number')
      const searchQuery = ref('')
      const customers = ref([])
      const loading = ref(false)

      const search = () => {
        if (searchCriteria.value && searchQuery.value) {
          loading.value = true
          router.get(
            route('customer.search'),
            { criteria: searchCriteria.value, query: searchQuery.value },
            {
              preserveState: true,
              preserveScroll: true,
              only: ['customers'],
              onSuccess: (page) => {
                customers.value = page.props.customers
                loading.value = false
              },
              onError: () => {
                loading.value = false
                // You might want to show an error message here
              }
            }
          )
        } else {
          customers.value = []
        }
      }

      const debounceSearch = debounce(() => {
        if (searchQuery.value.length > 2) {
          search()
        } else {
          customers.value = []
        }
      }, 300)

      watch(searchCriteria, () => {
        searchQuery.value = ''
        customers.value = []
      })

      return {
        searchCriteria,
        searchQuery,
        customers,
        loading,
        debounceSearch,
      }
    },
  }
  </script>



<style scoped>
.card {
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>


