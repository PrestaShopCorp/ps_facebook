import {RequestState} from '@/store/modules/catalog/types';

export type State = {
  warmedUp: boolean,
  enabledFeature: boolean,
  exportOn: boolean,
  categoryMatching?: CategoryMatchingStatus,
  report: ProductFeedReport,
  requests: CatalogRequests,
}

export type CategoryMatchingStatus = {
  matchingDone: boolean,
  matchingProgress: {
    matched: number,
    total: number,
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
}

export const state: State = {
  warmedUp: false,
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
  requests: {
    syncToggle: RequestState.IDLE,
    scan: RequestState.IDLE,
    requestNextSyncFull: RequestState.IDLE,
  },
};
