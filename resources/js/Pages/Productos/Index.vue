<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'

interface Producto {
  id: string
  categoriaId: string
  codigo: string
  nombre: string
  descripcion?: string
  precioUnitario: number
  stock: number
  tipo: 'bien' | 'servicio'
  activo: boolean
  categoria?: { id: string; nombre: string }
}

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UBadge = resolveComponent('UBadge')
const UModal = resolveComponent('UModal')

const props = defineProps<{
  productos: { data: Producto[] }
}>()

const toast = useToast()
const table = useTemplateRef('table')
const globalFilter = ref('')
const rowSelection = ref({})
const isDeleteModalOpen = ref(false)
const productoToDelete = ref<Producto | null>(null)

const handleCreate = () => router.visit(route('productos.create'))
const handleEdit = (producto: Producto) => router.visit(route('productos.edit', producto.id))

const confirmDelete = (producto: Producto) => {
  productoToDelete.value = producto
  isDeleteModalOpen.value = true
}

const handleDelete = async () => {
  if (!productoToDelete.value) return
  try {
    const response = await fetch(route('api.productos.destroy', productoToDelete.value.id), {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include'
    })
    if (response.ok) {
      toast.add({ title: 'Producto eliminado', color: 'success' })
      router.reload()
    } else {
      const error = await response.json()
      toast.add({ title: 'Error', description: error.message, color: 'error' })
    }
  } finally {
    isDeleteModalOpen.value = false
    productoToDelete.value = null
  }
}

function getRowItems(row: Row<Producto>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Editar producto', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Eliminar producto', icon: 'i-lucide-trash', color: 'error', onSelect() { confirmDelete(row.original) } }
  ]
}

const columns: TableColumn<Producto>[] = [
  {
    accessorKey: 'codigo',
    header: 'Codigo',
    cell: ({ row }) => h('span', { class: 'font-mono' }, row.original.codigo)
  },
  {
    accessorKey: 'nombre',
    header: 'Nombre',
    cell: ({ row }) => h('div', [
      h('p', { class: 'font-medium' }, row.original.nombre),
      h('p', { class: 'text-xs text-muted' }, row.original.categoria?.nombre || 'Sin categoria')
    ])
  },
  {
    accessorKey: 'tipo',
    header: 'Tipo',
    cell: ({ row }) => h(UBadge, {
      color: row.original.tipo === 'bien' ? 'info' : 'warning',
      variant: 'subtle'
    }, () => row.original.tipo === 'bien' ? 'Bien' : 'Servicio')
  },
  {
    accessorKey: 'stock',
    header: 'Stock',
    cell: ({ row }) => h('span', {}, row.original.stock)
  },
  {
    accessorKey: 'precioUnitario',
    header: 'Precio',
    cell: ({ row }) => h('span', { class: 'font-semibold text-primary' }, `$${Number(row.original.precioUnitario).toFixed(2)}`)
  },
  {
    accessorKey: 'activo',
    header: 'Estado',
    cell: ({ row }) => h(UBadge, {
      color: row.original.activo ? 'success' : 'error',
      variant: 'subtle'
    }, () => row.original.activo ? 'Activo' : 'Inactivo')
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
  <UDashboardPanel id="productos">
    <template #header>
      <UDashboardNavbar title="Productos">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right>
          <UButton label="Nuevo Producto" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar producto..." />
      </div>

      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        :data="productos.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">Total: {{ productos.data.length }} productos</div>
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
