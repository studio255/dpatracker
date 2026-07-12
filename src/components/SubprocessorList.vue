<script setup lang="ts">
import { generateUrl } from '@nextcloud/router'
import { t } from '@nextcloud/l10n'
import type { Subprocessor } from '../types'
import NcActions from '@nextcloud/vue/components/NcActions'
import NcActionButton from '@nextcloud/vue/components/NcActionButton'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'
import NcEmptyContent from '@nextcloud/vue/components/NcEmptyContent'

const exportUrl = generateUrl('/apps/dpatracker/export')

defineProps<{
	items: Subprocessor[]
	loading: boolean
}>()

const emit = defineEmits<{
	edit: [item: Subprocessor]
	delete: [id: number]
	create: []
}>()
</script>

<template>
	<div :class="$style.wrapper">
		<div :class="$style.toolbar">
			<NcButton
				:href="exportUrl"
				target="_blank"
				:text="t('dpatracker', 'Export PDF')"
				:aria-label="t('dpatracker', 'Export PDF')" />
			<NcButton type="primary" :text="t('dpatracker', 'Add subprocessor')" @click="emit('create')" />
		</div>

		<NcLoadingIcon v-if="loading" :size="32" :class="$style.spinner" />

		<NcEmptyContent
			v-else-if="!items.length"
			:name="t('dpatracker', 'No subprocessors yet')"
			:description="t('dpatracker', 'Add your first subprocessor to start your Art. 30 record.')" />

		<table v-else :class="$style.table">
			<thead>
				<tr>
					<th>{{ t('dpatracker', 'Name') }}</th>
					<th>{{ t('dpatracker', 'Purpose') }}</th>
					<th>{{ t('dpatracker', 'Location') }}</th>
					<th>{{ t('dpatracker', 'US parent') }}</th>
					<th>{{ t('dpatracker', 'Review date') }}</th>
					<th />
				</tr>
			</thead>
			<tbody>
				<tr v-for="item in items" :key="item.id">
					<td :class="$style.name">
						{{ item.name }}
					</td>
					<td :class="$style.ellipsis">
						{{ item.purpose ?? '—' }}
					</td>
					<td>{{ item.location ?? '—' }}</td>
					<td>
						<span :class="item.usParent ? $style.riskYes : $style.riskNo">
							{{ item.usParent ? t('dpatracker', 'Yes') : t('dpatracker', 'No') }}
						</span>
					</td>
					<td>{{ item.reviewDate ? item.reviewDate.split('-').reverse().join('.') : '—' }}</td>
					<td :class="$style.actions">
						<NcActions>
							<NcActionButton @click="emit('edit', item)">
								{{ t('dpatracker', 'Edit') }}
							</NcActionButton>
							<NcActionButton :class="$style.deleteAction" @click="emit('delete', item.id)">
								{{ t('dpatracker', 'Delete') }}
							</NcActionButton>
						</NcActions>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<style module>
.wrapper {
	width: 100%;
	max-width: 1000px;
	margin: 0 auto;
}

.toolbar {
	display: flex;
	justify-content: flex-end;
	margin-bottom: 12px;
}

.spinner {
	margin: 40px auto;
	display: block;
}

.table {
	width: 100%;
	border-collapse: collapse;
}

.table th,
.table td {
	padding: 10px 12px;
	text-align: left;
	border-bottom: 1px solid var(--color-border);
}

.table thead th {
	font-weight: 600;
	color: var(--color-text-maxcontrast);
	font-size: 0.85em;
	text-transform: uppercase;
	letter-spacing: 0.04em;
}

.name {
	font-weight: 600;
}

.ellipsis {
	max-width: 220px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.actions {
	width: 48px;
	text-align: right;
}

.riskYes {
	color: #d0021b;
	font-weight: 700;
}

.riskNo {
	color: #1a9c3e;
	font-weight: 600;
}

.deleteAction {
	color: var(--color-error);
}
</style>
