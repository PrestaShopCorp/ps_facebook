export type State = {
  warmedUp: boolean;
  externalBusinessID: string|null;
  onboarded: OnboardingContext;
}

export type OnboardingContext = {
  user: OnboardedUser|null;
  pixel: OnboardedPixel|null;
  facebookBusinessManager: OnboardedBusinessManager|null;
  page: OnboardedPage|null;
  ads: OnboardedAds|null;
  catalog: OnboardedCatalog|null;
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
  warmedUp: false,
  externalBusinessID: null,
  onboarded: {
    user: null,
    pixel: null,
    facebookBusinessManager: null,
    page: null,
    ads: null,
    catalog: null,
  },
};
