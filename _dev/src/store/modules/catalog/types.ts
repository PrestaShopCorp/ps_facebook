export type PreScanIntermediateResponseDto = {
  success: boolean,
  complete: false,

  /* eslint-disable camelcase */
  page_done: number,
  progress: number,
};
export type PreScanCompleteResponseDto = {
  success: boolean,
  complete: true,

  prevalidation: {
    syncable: number;
    notSyncable: number;
    lastScanDate: string;
  },
};
