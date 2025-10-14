<template>
  <section class="flex flex-col">
    <Tabs
      v-model="selectedTab"
      default-value="list"
      class="flex flex-col gap-2"
    >
      <TabsList class="flex flex-row justify-between md:justify-start">
        <TabsTrigger value="list"><IconList /> Liste </TabsTrigger>
        <TabsTrigger value="kanban"><IconKanban /> Tableau </TabsTrigger>
      </TabsList>

      <TabsContent
        value="list"
        class="flex flex-col gap-6"
      >
        <template
          v-for="(state, index) in states"
          :key="index"
        >
          <template v-if="tasksQueries[index]?.data?.member">
            <DataTableTeraLandTask
              :data="tasksQueries[index].data.member"
              :state="state"
              :total-items="tasksQueries[index]?.data?.totalItems"
            />
          </template>
        </template>
      </TabsContent>
      <TabsContent value="kanban">
        <Kanban class="flex flex-col gap-4 md:grid md:grid-cols-3">
          <KanbanColumn
            v-for="(state, index) in states"
            :key="state"
            :state="state"
            :total-items="tasksQueries[index]?.data?.totalItems"
            class="h-full"
          >
            <template #state>
              <BadgeTeraLandTaskState :state />
            </template>
            <template
              v-if="state === LandTaskState.to_be_done"
              #actions
            >
              <DialogTeraLandTaskCreate :land="land">
                <Button
                  variant="ghost"
                  size="xs"
                >
                  <template #icon><IconPlus /></template
                ></Button>
              </DialogTeraLandTaskCreate>
            </template>
            <template v-if="tasksQueries[index]?.data?.member">
              <KanbanItem
                v-for="landTask in tasksQueries[index].data.member"
                :key="landTask.ulid"
                :state="state"
              >
                <CardTeraLandTask
                  :land-task="landTask"
                  no-state
                  class="cursor-pointer"
                  @click="openDialog(landTask)"
                />
              </KanbanItem>
            </template>
          </KanbanColumn>
        </Kanban>
      </TabsContent>
      <TabsContent value="gantt">
        <SectionDevelopmentInProgress />
        <!--<Gantt
          :tasks="[
            { id: 1, name: 'Task A', start: '2025-03-01', end: '2025-03-05' },
            { id: 2, name: 'Task B', start: '2025-03-03', end: '2025-03-10' },
          ]"
        />-->
      </TabsContent>
    </Tabs>
    <DialogTeraLandTaskUpdate
      v-if="land && currentLandTask"
      v-model:open="isDialogForTaskVisible"
      :land-task="currentLandTask"
    />
  </section>
</template>

<script lang="ts" setup>
import { INJECTION_KEY_LAND } from '@/layouts/land-layout';
import CardTeraLandTask from '@lychen/vue-tera/components/land-task/card/CardTeraLandTask.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import Button from '@lychen/vue-components-core/button/Button.vue';
import Kanban from '@lychen/vue-components-extra/kanban/Kanban.vue';
import KanbanColumn from '@lychen/vue-components-extra/kanban/KanbanColumn.vue';
import { useQueries, useQuery } from '@tanstack/vue-query';
import { computed, inject, watch, ref, provide, onMounted } from 'vue';
import KanbanItem from '@lychen/vue-components-extra/kanban/KanbanItem.vue';
import { Tabs, TabsList, TabsTrigger } from '@lychen/vue-components-core/tabs';
import TabsContent from '@lychen/vue-components-core/tabs/TabsContent.vue';
import DataTableTeraLandTask from '@lychen/vue-tera/components/land-task/data-table/DataTableTeraLandTask.vue';
import BadgeTeraLandTaskState from '@lychen/vue-tera/components/land-task/badges/state/BadgeTeraLandTaskState.vue';
import DialogTeraLandTaskUpdate from '@lychen/vue-tera/components/land-task/dialogs/update/DialogTeraLandTaskUpdate.vue';
import DialogTeraLandTaskCreate from '@lychen/vue-tera/components/land-task/dialogs/create/DialogTeraLandTaskCreate.vue';
import {
  LandTaskJsonldState as LandTaskState,
  type components,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import SectionDevelopmentInProgress from '@lychen/vue-components-app/section-development-in-progress/SectionDevelopmentInProgress.vue';
import { useEventBus } from '@vueuse/core';
import { EVENT_landTaskDeleteSucceeded } from '@lychen/vue-tera/events/LandTaskEvents';
import { useRoute } from 'vue-router';
import { INJECTKEY_DIALOG_LAND_TASK_UPDATE_LAND } from '@lychen/vue-tera/components/land-task/dialogs/update';
import IconPlus from '@lychen/vue-icons/IconPlus.vue';
import IconKanban from '@lychen/vue-icons/IconKanban.vue';
import IconList from '@lychen/vue-icons/IconList.vue';

const land = inject(INJECTION_KEY_LAND);
provide(INJECTKEY_DIALOG_LAND_TASK_UPDATE_LAND, land);

const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);

const { api } = useTeraApi();

const states = computed(() => Object.values(LandTaskState));

const queries = computed(() =>
  states.value.map((state) => {
    return {
      queryKey: ['landTasks', state, landUlid],
      queryFn: async () => {
        const response = await api.GET('/api/land_tasks', {
          params: {
            query: {
              land: landUlid.value!,
              'order[dueDate]': 'asc',
              state: state,
            },
          },
        });

        return response.data;
      },
      enabled,
    };
  }),
);

const tasksQueries = useQueries({ queries: queries });

function refetchAll() {
  tasksQueries.value.forEach((result) => result.refetch());
}

const { on: onDeleteTask } = useEventBus(EVENT_landTaskDeleteSucceeded);

onDeleteTask(() => {
  refetchAll();
});

const route = useRoute();
const taskIdFromURL = computed(() => route.query.taskId);

const { data: taskFromURL } = useQuery({
  queryKey: ['land-task', taskIdFromURL],
  queryFn: async () => {
    if (taskIdFromURL.value) {
      const response = await api.GET('/api/land_tasks/{ulid}', {
        params: { path: { ulid: taskIdFromURL.value as string } },
      });
      return response.data;
    }
    return null;
  },
  enabled: !!taskIdFromURL.value,
});

watch(taskFromURL, (newValue) => {
  currentLandTask.value = taskFromURL.value;
  isDialogForTaskVisible.value = true;
});

const isDialogForTaskVisible = ref(false);

const currentLandTask = ref();

function openDialog(landTask: components['schemas']['LandTask.jsonld']) {
  currentLandTask.value = landTask;
  isDialogForTaskVisible.value = true;
}

// Local Storage Logic
const localStorageKey = 'selected-land-tasks-tab';
const selectedTab = ref<string>('list');

onMounted(() => {
  const storedTab = localStorage.getItem(localStorageKey);
  if (storedTab) {
    selectedTab.value = storedTab;
  }
});

watch(selectedTab, (newTab) => {
  localStorage.setItem(localStorageKey, newTab);
});
</script>

<style lang="css" scoped></style>
