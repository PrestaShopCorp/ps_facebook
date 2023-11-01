import {RequestState} from '@/store/modules/catalog/types';

export type State = {
  warmedUp: RequestState,
  externalBusinessID: string|null;
  onboarded: OnboardingContext|null;
}

export type OnboardingContext = {
  user: OnboardedUser;
  pixel: OnboardedPixel;
  facebookBusinessManager: OnboardedBusinessManager;
  page: OnboardedPage;
  ads: OnboardedAds;
  catalog: OnboardedCatalog;
}

export type OnboardedUser = {
  email: string;
}

export type OnboardedPixel = {
  id: string;
  name: string;
  lastActive: null|string;
  isUnavailable: boolean;
  isActive: boolean;
}

export type OnboardedBusinessManager = {
  id: string;
  name: string;
  createDate: null|string;
}

export type OnboardedPage = {
  id: string;
  page: string;
  likes: number;
  logo: string;
}

export type OnboardedAds = {
  id: string;
  name: string;
  createdAt: null|string;
}

export type OnboardedCatalog = {
  id: string;
  productSyncStarted: boolean;
  categoryMatchingStarted: boolean;
}

export const state: State = {
  warmedUp: RequestState.IDLE,
  externalBusinessID: null,
  onboarded: null,
};
