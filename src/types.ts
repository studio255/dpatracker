export interface Subprocessor {
	id: number
	userId: string
	name: string
	purpose: string | null
	dataCategories: string | null
	location: string | null
	usParent: boolean
	dpaFileId: number | null
	dpaFileName: string | null
	reviewDate: string | null
	createdAt: string | null
}

export type SubprocessorPayload = Omit<Subprocessor, 'id' | 'userId' | 'createdAt'>
