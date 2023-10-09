import { OnboardingContext, State } from "@/store/modules/onboarding/state";

export const contextFacebookOnboarded: OnboardingContext = {
  user: {
    email: "him@prestashop.com",
  },
  facebookBusinessManager: {
    name: "La Fanchonette",
    createDate: Date.now().toString(),
    id: "12345689",
  },
  pixel: {
    name: "La Fanchonette Test Pixel",
    id: "1234567890",
    lastActive: Date.now().toString(),
    isActive: true,
    isUnavailable: false,
  },
  page: {
    id: '34555874367890',
    page: "La Fanchonette",
    likes: 42,
    logo: 'https://oh-no',
  },
  ads: {
    name: "La Fanchonette",
    id: "12541233",
    createdAt: Date.now().toString(),
  },
  catalog: {
    id: '34567890',
    categoryMatchingStarted: false,
    productSyncStarted: false,
  },
};

export const stateOnboarded: State = {
  warmedUp: true,
  externalBusinessID: 'xxxxx-xxxxxx-xxxxx-xx',
  onboarded: contextFacebookOnboarded,
};
