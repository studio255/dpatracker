<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { t } from '@nextcloud/l10n'
import type { Subprocessor, SubprocessorPayload } from './types'
import { fetchAll, createOne, updateOne, deleteOne } from './api'
import SubprocessorList from './components/SubprocessorList.vue'
import SubprocessorForm from './components/SubprocessorForm.vue'
import NcAppContent from '@nextcloud/vue/components/NcAppContent'
import NcContent from '@nextcloud/vue/components/NcContent'

const items = ref<Subprocessor[]>([])
const loading = ref(true)
const formOpen = ref(false)
const editing = ref<Subprocessor | null>(null)
const globalError = ref('')

async function load() {
	loading.value = true
	globalError.value = ''
	try {
		items.value = await fetchAll()
	} catch (e) {
		globalError.value = (e as Error).message
	} finally {
		loading.value = false
	}
}

function openCreate() {
	editing.value = null
	formOpen.value = true
}

function openEdit(item: Subprocessor) {
	editing.value = item
	formOpen.value = true
}

async function onSave(payload: SubprocessorPayload) {
	try {
		if (editing.value) {
			await updateOne(editing.value.id, payload)
		} else {
			await createOne(payload)
		}
		formOpen.value = false
		await load()
	} catch (e) {
		globalError.value = (e as Error).message
	}
}

async function onDelete(id: number) {
	if (!confirm(t('dpatracker', 'Delete this subprocessor?'))) return
	try {
		await deleteOne(id)
		await load()
	} catch (e) {
		globalError.value = (e as Error).message
	}
}

onMounted(load)
</script>

<template>
	<NcContent app-name="dpatracker">
		<NcAppContent :class="$style.content">
			<p v-if="globalError" :class="$style.error">
				{{ globalError }}
			</p>

			<SubprocessorList
				:items="items"
				:loading="loading"
				@create="openCreate"
				@edit="openEdit"
				@delete="onDelete" />

			<SubprocessorForm
				v-model:open="formOpen"
				:item="editing"
				@save="onSave" />
		</NcAppContent>
	</NcContent>
</template>

<style module>
.content {
	padding: 20px 24px;
}

.error {
	color: var(--color-error);
	margin-bottom: 12px;
}
</style>
