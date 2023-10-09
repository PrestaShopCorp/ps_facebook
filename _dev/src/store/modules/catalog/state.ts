export type State = {
  warmedUp: boolean,
  enabledFeature: boolean,
  exportOn?: boolean,
  categoryMatching?: CategoryMatchingStatus,
  report: ProductFeedReport,
}

export type CategoryMatchingStatus = {
  matchingDone: boolean,
  matchingProgress: {
    matched: number,
    total: number,
  }
}

export type ProductFeedReport = {
  prevalidation: {
    syncable: number,
    notSyncable: number,
    lastScanDate: Date,
  }|null,
  reporting: {
    catalog: number,
    errored: number,
    lastSyncDate: Date,
  }|null,
}

export const state: State = {
  warmedUp: false,
  enabledFeature: false,
  report: {
    prevalidation: null,
    reporting: null,
  },
};
