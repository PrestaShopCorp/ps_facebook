import {ValidationReport} from './state';

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

  prevalidation: ValidationReport,
};

export enum RequestState {
  IDLE = 'IDLE',
  PENDING = 'PENDING',
  FAILED = 'FAILED',
  SUCCESS = 'SUCCESS',
}
