<script setup lang="ts">
import { ref, watch } from 'vue'
import { getCurrentUser } from '@nextcloud/auth'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import type { Subprocessor, SubprocessorPayload } from '../types'
import NcDialog from '@nextcloud/vue/components/NcDialog'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcTextField from '@nextcloud/vue/components/NcTextField'
import NcTextArea from '@nextcloud/vue/components/NcTextArea'

const props = defineProps<{
	open: boolean
	item: Subprocessor | null
}>()

const emit = defineEmits<{
	'update:open': [value: boolean]
	save: [payload: SubprocessorPayload]
}>()

const name = ref('')
const purpose = ref('')
const dataCategories = ref('')
const location = ref('')
const usParent = ref(false)
const dpaFileId = ref<number | null>(null)
const dpaFileName = ref<string | null>(null)
const reviewDate = ref('')
const saving = ref(false)
const error = ref('')

watch(() => props.open, (val) => {
	if (!val) return
	error.value = ''
	saving.value = false
	if (props.item) {
		name.value = props.item.name
		purpose.value = props.item.purpose ?? ''
		dataCategories.value = props.item.dataCategories ?? ''
		location.value = props.item.location ?? ''
		usParent.value = props.item.usParent
		dpaFileId.value = props.item.dpaFileId
		dpaFileName.value = props.item.dpaFileName
		reviewDate.value = props.item.reviewDate ?? ''
	} else {
		name.value = ''
		purpose.value = ''
		dataCategories.value = ''
		location.value = ''
		usParent.value = false
		dpaFileId.value = null
		dpaFileName.value = null
		reviewDate.value = ''
	}
})

async function resolveFileId(path: string): Promise<number | null> {
	const uid = getCurrentUser()?.uid ?? ''
	try {
		const res = await fetch(`/remote.php/dav/files/${encodeURIComponent(uid)}${path}`, {
			method: 'PROPFIND',
			headers: { Depth: '0', 'Content-Type': 'application/xml' },
			body: '<?xml version="1.0"?><d:propfind xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns"><d:prop><oc:fileid/></d:prop></d:propfind>',
		})
		const text = await res.text()
		const match = text.match(/<oc:fileid>(\d+)<\/oc:fileid>/)
		return match ? parseInt(match[1], 10) : null
	} catch {
		return null
	}
}

function openFilePicker() {
	// eslint-disable-next-line @typescript-eslint/no-explicit-any
	(window as any).OC.dialogs.filepicker(
		t('dpatracker', 'Select DPA document'),
		async (path: string) => {
			if (!path || typeof path !== 'string') return
			dpaFileName.value = path.split('/').pop() ?? path
			dpaFileId.value = await resolveFileId(path)
		},
		false,
		[],
		true,
	)
}

function clearFile() {
	dpaFileId.value = null
	dpaFileName.value = null
}

async function submit() {
	if (!name.value.trim()) {
		error.value = t('dpatracker', 'Name is required.')
		return
	}
	saving.value = true
	error.value = ''
	try {
		emit('save', {
			name: name.value.trim(),
			purpose: purpose.value || null,
			dataCategories: dataCategories.value || null,
			location: location.value || null,
			usParent: usParent.value,
			dpaFileId: dpaFileId.value,
			dpaFileName: dpaFileName.value,
			reviewDate: reviewDate.value || null,
		})
	} finally {
		saving.value = false
	}
}
</script>

<template>
	<NcDialog
		:open="open"
		:name="item ? t('dpatracker', 'Edit subprocessor') : t('dpatracker', 'Add subprocessor')"
		:close-on-click-outside="false"
		@update:open="emit('update:open', $event)">
		<form :class="$style.form" @submit.prevent="submit">
			<NcTextField
				v-model="name"
				:label="t('dpatracker', 'Name')"
				:placeholder="t('dpatracker', 'e.g. AWS S3')"
				required />

			<NcTextArea
				v-model="purpose"
				:label="t('dpatracker', 'Purpose of processing')"
				:placeholder="t('dpatracker', 'e.g. File storage for customer documents')"
				:rows="3" />

			<NcTextArea
				v-model="dataCategories"
				:label="t('dpatracker', 'Categories of personal data')"
				:placeholder="t('dpatracker', 'e.g. Names, email addresses, documents')"
				:rows="3" />

			<NcTextField
				v-model="location"
				:label="t('dpatracker', 'Location / legal seat')"
				:placeholder="t('dpatracker', 'e.g. EU (Frankfurt)')" />

			<div :class="$style.checkboxRow">
				<input
					id="us-parent-cb"
					v-model="usParent"
					type="checkbox"
					:class="$style.checkbox">
				<label for="us-parent-cb">{{ t('dpatracker', 'Has US parent company (CLOUD Act risk)') }}</label>
			</div>

			<div :class="$style.dateRow">
				<label for="review-date-input">{{ t('dpatracker', 'Review date') }}</label>
				<input
					id="review-date-input"
					v-model="reviewDate"
					type="date"
					:class="$style.dateInput">
			</div>

			<div :class="$style.fileRow">
				<span :class="$style.fileLabel">{{ t('dpatracker', 'DPA document') }}</span>
				<a
					v-if="dpaFileName && dpaFileId"
					:href="generateUrl('/f/' + dpaFileId)"
					target="_blank"
					:class="$style.fileLink">
					📄 {{ dpaFileName }}
				</a>
				<span v-else-if="dpaFileName" :class="$style.fileLink">📄 {{ dpaFileName }}</span>
				<span v-else :class="$style.fileEmpty">{{ t('dpatracker', 'No document attached') }}</span>
				<NcButton
					:text="t('dpatracker', 'Select file')"
					@click.prevent="openFilePicker" />
				<NcButton
					v-if="dpaFileId"
					:text="t('dpatracker', 'Remove')"
					@click.prevent="clearFile" />
			</div>

			<p v-if="error" :class="$style.error">
				{{ error }}
			</p>
		</form>

		<template #actions>
			<NcButton :disabled="saving" :text="t('dpatracker', 'Cancel')" @click="emit('update:open', false)" />
			<NcButton type="primary"
				:disabled="saving"
				:text="saving ? t('dpatracker', 'Saving…') : t('dpatracker', 'Save')"
				@click="submit" />
		</template>
	</NcDialog>
</template>

<style module>
.form {
	display: flex;
	flex-direction: column;
	gap: 16px;
	padding: 4px 0;
}

.fileRow {
	display: flex;
	align-items: center;
	gap: 8px;
	flex-wrap: wrap;
}

.fileLabel {
	font-weight: 600;
	min-width: 120px;
}

.fileLink {
	color: var(--color-primary-element);
	text-decoration: none;
	flex: 1;
}

.fileLink:hover {
	text-decoration: underline;
}

.fileEmpty {
	color: var(--color-text-maxcontrast);
	font-style: italic;
	flex: 1;
}

.dateRow {
	display: flex;
	flex-direction: column;
	gap: 4px;
}

.dateRow label {
	font-size: 0.875em;
	color: var(--color-text-maxcontrast);
}

.dateInput {
	width: 100%;
	padding: 6px 10px;
	border: 2px solid var(--color-border-dark);
	border-radius: var(--border-radius);
	background: var(--color-main-background);
	color: var(--color-main-text);
	font-size: 1em;
	cursor: pointer;
}

.dateInput:focus {
	outline: none;
	border-color: var(--color-primary-element);
	box-shadow: 0 0 0 2px var(--color-primary-element-light);
}

.checkboxRow {
	display: flex;
	align-items: center;
	gap: 8px;
}

.checkbox {
	width: 16px;
	height: 16px;
	cursor: pointer;
	flex-shrink: 0;
}

.checkboxRow label {
	cursor: pointer;
	user-select: none;
}

.error {
	color: var(--color-error);
	font-size: 0.9em;
	margin: 0;
}
</style>
