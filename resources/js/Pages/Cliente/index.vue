<script setup lang="ts">
import { ref, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'
import type { Cliente } from '../../types'

const UAvatar = resolveComponent('UAvatar')
const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')

const props = defineProps<{
  customers: {
    data: Cliente[]
    links: any[]
    meta: any
  }
}>()

const toast = useToast()
const table = useTemplateRef('table')
const globalFilter = ref('')
const rowSelection = ref({})

const isDeleteModalOpen = ref(false)
const clienteToDelete = ref<Cliente | null>(null)

const handleCreate = () => router.visit(route('clientes.create'))
const handleEdit = (cliente: Cliente) => router.visit(route('clientes.edit', cliente.id))

const confirmDelete = (cliente: Cliente) => {
  clienteToDelete.value = cliente
  isDeleteModalOpen.value = true
}

const handleDelete = () => {
  if (!clienteToDelete.value) return

  router.delete(route('clientes.destroy', clienteToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false
      clienteToDelete.value = null
    }
  })
}

function getRowItems(row: Row<Cliente>) {
  return [
    { type: 'label', label: 'Acciones' },
    { label: 'Editar cliente', icon: 'i-lucide-pencil', onSelect() { handleEdit(row.original) } },
    { type: 'separator' },
    { label: 'Copiar ID', icon: 'i-lucide-copy', onSelect() {
      navigator.clipboard.writeText(row.original.id.toString())
      toast.add({ title: 'ID copiado' })
    }},
    { type: 'separator' },
    { label: 'Eliminar cliente', icon: 'i-lucide-trash', color: 'error', onSelect() { confirmDelete(row.original) } }
  ]
}

const columns: TableColumn<Cliente>[] = [
  {
    accessorKey: 'razonSocial',
    header: 'Cliente',
    cell: ({ row }) => h('div', { class: 'flex items-center gap-3' }, [
      h(UAvatar, {
        src: `https://ui-avatars.com/api/?name=${encodeURIComponent(row.original.razonSocial)}&background=random`,
        alt: row.original.razonSocial,
        size: 'lg'
      }),
      h('div', [
        h('p', { class: 'font-medium text-highlighted' }, row.original.razonSocial),
        h('p', { class: 'text-xs text-muted' }, row.original.email)
      ])
    ])
  },
  {
    accessorKey: 'numeroDocumento',
    header: 'Documento',
    cell: ({ row }) => row.original.numeroDocumento
  },
  {
    accessorKey: 'telefono',
    header: 'Telefono',
    cell: ({ row }) => row.original.telefono || '-'
  },
  {
    accessorKey: 'direccion',
    header: 'Direccion',
    cell: ({ row }) => row.original.direccion || '-'
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
  <UDashboardPanel id="clientes">
    <template #header>
      <UDashboardNavbar title="Clientes">
        <template #leading><UDashboardSidebarCollapse /></template>
        <template #right>
          <UButton label="Nuevo Cliente" color="primary" icon="i-lucide-plus" @click="handleCreate" />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5 mb-4">
        <UInput v-model="globalFilter" class="w-full sm:w-1/2" size="xl" icon="i-lucide-search" placeholder="Buscar cliente..." />
      </div>

      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{ getPaginationRowModel: getPaginationRowModel() }"
        :filter-options="{ getFilteredRowModel: getFilteredRowModel() }"
        :data="customers.data"
        :columns="columns"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">Total: {{ customers.data.length }} clientes</div>
        <UPagination
          :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
          :items-per-page="table?.tableApi?.getState().pagination.pageSize"
          :total="table?.tableApi?.getFilteredRowModel().rows.length"
          @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
        />
      </div>
    </template>
  </UDashboardPanel>

  <!-- Modal de confirmación de eliminación -->
  <UModal
    v-model:open="isDeleteModalOpen"
    title="Confirmar eliminación"
    :description="`¿Está seguro de que desea eliminar el cliente ${clienteToDelete?.razonSocial}? Esta acción no se puede deshacer.`"
  >
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton label="Eliminar" color="error" variant="solid" icon="i-lucide-trash" @click="handleDelete" />
        <UButton label="Cancelar" color="neutral" variant="subtle" @click="isDeleteModalOpen = false" />
      </div>
    </template>
  </UModal>
</template>
