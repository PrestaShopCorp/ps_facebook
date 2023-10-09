export type State = {
  enabledFeature: boolean,
  exportOn?: boolean,
  categoryMatching?: CategoryMatchingStatus,
  report?: ProductFeedReport,
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
    lastScanDate: string,
  },
  reporting: {
    catalog: number,
    errored: number,
    lastSyncDate: string,
  },
}

export const state: State = {
  enabledFeature: false,
};
