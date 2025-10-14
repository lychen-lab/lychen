<template>
  <div class="flex flex-col gap-2">
    <div class="flex flex-row items-center justify-between gap-2">
      <div class="flex flex-row items-center gap-2">
        <BadgeTeraLandTaskState
          v-if="state"
          :state
        />
        <small class="opacity-50">{{ totalItems }}</small>
      </div>
      <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button
            variant="ghost"
            class="ml-auto"
            size="sm"
          >
            Champs
            <IconChevronDown class="ml-2 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuCheckboxItem
            v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
            :key="column.id"
            class="capitalize"
            :model-value="column.getIsVisible()"
            @update:model-value="
              (value) => {
                column.toggleVisibility(!!value);
              }
            "
          >
            {{ column.id }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
    <Table>
      <TableHeader>
        <TableRow
          v-for="headerGroup in table.getHeaderGroups()"
          :key="headerGroup.id"
        >
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
            :data-pinned="header.column.getIsPinned()"
            :class="
              cn(
                { 'sticky bg-surface-container/95': header.column.getIsPinned() },
                header.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
              )
            "
          >
            <FlexRender
              v-if="!header.isPlaceholder"
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <template v-if="table.getRowModel().rows?.length">
          <template
            v-for="row in table.getRowModel().rows"
            :key="row.id"
          >
            <TableRow
              :data-state="row.getIsSelected() && 'selected'"
              class="cursor-pointer"
              @click="openDialog(row.original)"
            >
              <TableCell
                v-for="cell in row.getVisibleCells()"
                :key="cell.id"
                :data-pinned="cell.column.getIsPinned()"
                :class="
                  cn(
                    { 'sticky bg-surface-container/95': cell.column.getIsPinned() },
                    cell.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
                  )
                "
              >
                <FlexRender
                  :render="cell.column.columnDef.cell"
                  :props="cell.getContext()"
                />
              </TableCell>
            </TableRow>
            <TableRow v-if="row.getIsExpanded()">
              <TableCell :colspan="row.getAllCells().length">
                {{ JSON.stringify(row.original) }}
              </TableCell>
            </TableRow>
          </template>
        </template>

        <TableRow v-else>
          <TableCell
            :colspan="columns.length"
            class="h-24 text-center"
          >
            No results.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
    <DialogTeraLandTaskUpdate
      v-if="currentLandTask"
      v-model:open="isDialogForTaskVisible"
      :land-task="currentLandTask"
    />
  </div>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { h, ref, computed, type Ref } from 'vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@lychen/vue-components-core/table';
import type {
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  Updater,
  VisibilityState,
} from '@tanstack/vue-table';
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table';
import { Checkbox } from '@lychen/vue-components-core/checkbox';
import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { DropdownMenu } from '@lychen/vue-components-core/dropdown-menu';
import DropdownMenuTrigger from '@lychen/vue-components-core/dropdown-menu/DropdownMenuTrigger.vue';
import DropdownMenuContent from '@lychen/vue-components-core/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuCheckboxItem from '@lychen/vue-components-core/dropdown-menu/DropdownMenuCheckboxItem.vue';

import BadgeTeraLandTaskState from '../badges/state/BadgeTeraLandTaskState.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import DropdownMenuTeraLandTaskMain from '../dropdown-menu/DropdownMenuTeraLandTaskMain.vue';
import DialogTeraLandTaskUpdate from '../dialogs/update/DialogTeraLandTaskUpdate.vue';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const { d } = useI18nExtended();

const {
  data,
  state = undefined,
  totalItems = undefined,
} = defineProps<{
  data: components['schemas']['LandTask.jsonld'][] | undefined;
  state?: components['schemas']['LandTask.jsonld']['state'];
  totalItems?: number;
}>();
const columnHelper = createColumnHelper<components['schemas']['LandTask.jsonld']>();

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function valueUpdater<T extends Updater<any>>(updaterOrValue: T, ref: Ref) {
  ref.value = typeof updaterOrValue === 'function' ? updaterOrValue(ref.value) : updaterOrValue;
}

const dataForTable = computed(() => data || []);

const columns = [
  columnHelper.display({
    id: 'select',
    header: ({ table }) =>
      h(Checkbox, {
        modelValue:
          table.getIsAllPageRowsSelected() ||
          (table.getIsSomePageRowsSelected() && 'indeterminate'),
        'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
        ariaLabel: 'Select all',
      }),
    cell: ({ row }) => {
      return h(Checkbox, {
        modelValue: row.getIsSelected(),
        'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
        ariaLabel: 'Select row',
      });
    },
    enableSorting: false,
    enableHiding: false,
  }),
  columnHelper.accessor('title', {
    header: ({ column }) => {
      return h('div', { class: 'flex flex-row items-center gap-2' }, [
        'Nom',
        h(Button, {
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          label: 'sort',
          size: 'xs',
        }),
      ]);
    },
    cell: ({ row }) => h('div', { class: 'lowercase font-medium' }, row.getValue('title')),
  }),
  columnHelper.accessor('startDate', {
    header: ({ column }) =>
      h('div', { class: 'flex flex-row items-center gap-2' }, [
        'Début',
        h(Button, {
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          label: 'sort',
          size: 'xs',
        }),
      ]),
    cell: ({ row }) => {
      // Format the amount as a dollar amount
      const formatted = row.getValue('startDate') ? d(row.getValue('startDate'), 'short') : '';

      return h('div', { class: '' }, formatted);
    },
  }),
  columnHelper.accessor('dueDate', {
    header: ({ column }) =>
      h('div', { class: 'flex flex-row items-center gap-2' }, [
        'Échéance',
        h(Button, {
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          icon: 'sort-icon',
          size: 'xs',
        }),
      ]),
    cell: ({ row }) => {
      // Format the amount as a dollar amount
      const formatted = row.getValue('dueDate') ? d(row.getValue('dueDate'), 'short') : '';

      return h('div', { class: '' }, formatted);
    },
  }),
  columnHelper.accessor('ulid', {
    header: ({ column }) => h('div', { class: 'flex flex-row items-center gap-2' }, ['ID']),
    cell: ({ row }) => {
      return h('div', { class: 'truncate w-30' }, row.getValue('ulid'));
    },
  }),
  columnHelper.display({
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      const landTask: components['schemas']['LandTask.jsonld'] = row.original;
      return h(
        'div',
        { class: 'relative' },
        h(
          DropdownMenuTeraLandTaskMain,
          {
            'land-task': landTask,
          },
          {
            default: () =>
              h(Button, {
                label: 'ellipsis-vertical',
                size: 'sm',
                variant: 'ghost',
              }),
          },
        ),
      );
    },
  }),
];

const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const expanded = ref<ExpandedState>({});

const table = useVueTable({
  data: dataForTable,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() {
      return sorting.value;
    },
    get columnFilters() {
      return columnFilters.value;
    },
    get columnVisibility() {
      return columnVisibility.value;
    },
    get rowSelection() {
      return rowSelection.value;
    },
    get expanded() {
      return expanded.value;
    },
    columnPinning: {
      left: ['state'],
    },
  },
});

const isDialogForTaskVisible = ref(false);
const currentLandTask = ref<components['schemas']['LandTask.jsonld']>();

function openDialog(landTask: components['schemas']['LandTask.jsonld']) {
  currentLandTask.value = landTask;
  isDialogForTaskVisible.value = true;
}
</script>

<style lang="css" scoped></style>
