import {RequestState} from '@/store/types';

export type State = {
  warmedUp: RequestState,
  enabledFeature: boolean,
  exportOn: boolean,
  categoryMatching: CategoryMatchingStatus,
  report: ProductFeedReport,
  requests: CatalogRequests,
}

export type CategoryMatchingStatus = {
  matchingDone: boolean,
  matchingProgress: {
    matched: number|null,
    total: number|null,
  }
}

export type ValidationReport = {
  syncable: number|null,
  notSyncable: number|null,
  lastScanDate: Date|null,
}

export type SyncReport = {
  catalog: number|null,
  errored: number|null,
  lastSyncDate: Date|null,
}

export type ProductFeedReport = {
  prevalidation: ValidationReport,
  reporting: SyncReport,
}

export type CatalogRequests = {
  syncToggle: RequestState,
  scan: RequestState,
  requestNextSyncFull: RequestState,
  catalogReport: RequestState,
}

export const state: State = {
  warmedUp: RequestState.IDLE,
  enabledFeature: false,
  exportOn: false,
  report: {
    prevalidation: {
      syncable: null,
      notSyncable: null,
      lastScanDate: null,
    },
    reporting: {
      catalog: null,
      errored: null,
      lastSyncDate: null,
    },
  },
  categoryMatching: {
    matchingDone: false,
    matchingProgress: {
      matched: null,
      total: null,
    },
  },
  requests: {
    syncToggle: RequestState.IDLE,
    scan: RequestState.IDLE,
    requestNextSyncFull: RequestState.IDLE,
    catalogReport: RequestState.IDLE,
  },
};
