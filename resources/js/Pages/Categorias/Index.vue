<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface Categoria {
  id: string
  nombre: string
  descripcion?: string
  activo: boolean
}

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UBadge = resolveComponent('UBadge')
const UModal = resolveComponent('UModal')

const props = defineProps<{
  categorias: { data: Categoria[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')
const globalFilter = ref('')
const rowSelection = ref({})
const isDeleteModalOpen = ref(false)
const categoriaToDelete = ref<Categoria | null>(null)

const handleCreate = () => router.visit(route('categorias.create'))
const handleEdit = (categoria: Categoria) => router.visit(route('categorias.edit', categoria.id))

const confirmDelete = (categoria: Categoria) => {
  categoriaToDelete.value = categoria
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!categoriaToDelete.value) return
  try {
    const response = await fetch(route('api.categorias.destroy', categoriaToDelete.value.id), {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include'
    })
    if (response.ok) {
      toast.add({ title: 'Categoria eliminada', color: 'success' })
      router.reload()
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    isDeleteModalOpen.value = false
    categoriaToDelete.value = null
  }
}

function getRowItems(row: Row<Categoria>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Editar categoria', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Eliminar categoria', icon: 'i-lucide-trash', color: 'error', onSelect() { confirmDelete(row.original) } }
  ]
}

const columns: TableColumn<Categoria>[] = [
  {
    accessorKey: 'nombre',
    header: 'Nombre',
    cell: ({ row }) => h('span', { class: 'font-medium' }, row.original.nombre)
  },
  {
    accessorKey: 'descripcion',
    header: 'Descripcion',
    cell: ({ row }) => h('span', { class: 'text-muted' }, row.original.descripcion || 'Sin descripcion')
  },
  {
    accessorKey: 'activo',
    header: 'Estado',
    cell: ({ row }) => h(UBadge, {
      color: row.original.activo ? 'success' : 'error',
      variant: 'subtle'
    }, () => row.original.activo ? 'Activa' : 'Inactiva')
  },
  {
    id: 'actions',
    cell: ({ row }) => h('div', { class: 'text-right' },
      h(UDropdownMenu, { content: { align: 'end' }, items: getRowItems(row) }, () =>
        h(UButton, { icon: 'i-lucide-ellipsis-vertical', color: 'neutral', variant: 'ghost' })
      )
    )
  }
]

const pagination = ref({ pageIndex: 0, pageSize: 10 })
</script>

<template>
  <UDashboardPanel id="categorias">
    <template #header>
      <UDashboardNavbar title="Categorias">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right>
          <UButton label="Nueva Categoria" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar categoria..." />
      </div>

      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        :data="categorias.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">Total: {{ categorias.data.length }} categorias</div>
        <UPagination
          :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
          :items-per-page="table?.tableApi?.getState().pagination.pageSize"
          :total="table?.tableApi?.getFilteredRowModel().rows.length"
          @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
        />
      </div>
    </template>
  </UDashboardPanel>

  <UModal v-model:open="isDeleteModalOpen" title="Confirmar eliminacion">
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton label="Eliminar" color="error" @click="handleDelete" />
        <UButton label="Cancelar" color="neutral" variant="subtle" @click="isDeleteModalOpen = false" />
      </div>
    </template>
  </UModal>
</template>
