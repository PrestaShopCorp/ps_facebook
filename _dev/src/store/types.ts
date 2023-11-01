import {State as AppState} from './modules/app/state';
import {State as CatalogState} from './modules/catalog/state';
import {State as OnboardingState} from './modules/onboarding/state';

export interface FullState {
  app: AppState,
  catalog: CatalogState,
  onboarding: OnboardingState,
}

export enum RequestState {
  IDLE = 'IDLE',
  PENDING = 'PENDING',
  FAILED = 'FAILED',
  SUCCESS = 'SUCCESS',
}
