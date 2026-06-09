import type { Subprocessor, SubprocessorPayload } from './types'

const BASE = '/ocs/v2.php/apps/dpatracker/subprocessors'

async function ocsRequest<T>(path: string, options: RequestInit = {}): Promise<T> {
	const res = await fetch(BASE + path, {
		...options,
		headers: {
			'OCS-APIREQUEST': 'true',
			Accept: 'application/json',
			'Content-Type': 'application/json',
			...options.headers,
		},
	})
	const json = await res.json()
	if (!res.ok) {
		throw new Error(json?.ocs?.meta?.message ?? 'Request failed')
	}
	return json.ocs.data as T
}

export const fetchAll = (): Promise<Subprocessor[]> =>
	ocsRequest<Subprocessor[]>('')

export const fetchOne = (id: number): Promise<Subprocessor> =>
	ocsRequest<Subprocessor>(`/${id}`)

export const createOne = (payload: SubprocessorPayload): Promise<Subprocessor> =>
	ocsRequest<Subprocessor>('', {
		method: 'POST',
		body: JSON.stringify(payload),
	})

export const updateOne = (id: number, payload: SubprocessorPayload): Promise<Subprocessor> =>
	ocsRequest<Subprocessor>(`/${id}`, {
		method: 'PUT',
		body: JSON.stringify(payload),
	})

export const deleteOne = (id: number): Promise<void> =>
	ocsRequest<void>(`/${id}`, { method: 'DELETE' })
