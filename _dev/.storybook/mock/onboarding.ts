import { OnboardingContext, State } from "@/store/modules/onboarding/state";
import {RequestState} from "@/store/types";

export const contextFacebookOnboarded: OnboardingContext = {
  user: {
    email: "him@prestashop.com",
  },
  facebookBusinessManager: {
    name: "La Fanchonette",
    createDate: '2020-07-14T08:46:48+0000',
    id: "12345689",
  },
  pixel: {
    name: "La Fanchonette Test Pixel",
    id: "1234567890",
    lastActive: '2023-10-19T10:56:51+0100',
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
    createdAt: '2020-07-14T10:47:12+0200',
  },
  catalog: {
    id: '34567890',
    categoryMatchingStarted: false,
    productSyncStarted: false,
  },
};

export const stateOnboarded: State = {
  warmedUp: RequestState.SUCCESS,
  externalBusinessID: 'xxxxx-xxxxxx-xxxxx-xx',
  onboarded: contextFacebookOnboarded,
};
