import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {OnboardingContext} from './state';
import {runIf} from '@/lib/Promise';

export default {
  async [ActionsTypes.WARMUP_STORE](
    {dispatch, state, getters},
  ) {
    if (state.warmedUp) {
      return;
    }
    state.warmedUp = true;

    await runIf(
      !getters.GET_EXTERNAL_BUSINESS_ID,
      dispatch(ActionsTypes.REQUEST_EXTERNAL_BUSINESS_ID),
    );
    await runIf(
      !getters.IS_USER_ONBOARDED,
      dispatch(ActionsTypes.REQUEST_ONBOARDING_STATE),
    );
  },

  async [ActionsTypes.REQUEST_EXTERNAL_BUSINESS_ID]({commit}): Promise<string> {
    const json: {externalBusinessId: string} = await fetchShop(
      'RetrieveExternalBusinessId',
    );
    commit(MutationsTypes.SET_EXTERNAL_BUSINESS_ID, json.externalBusinessId);
    return json.externalBusinessId;
  },

  async [ActionsTypes.REQUEST_ONBOARDING_STATE]({commit, state}) {
    const json: {
      psFacebookExternalBusinessId: string,
      contextPsFacebook: OnboardingContext
    } = await fetchShop('GetFbContext');

    if (state.externalBusinessID
      && state.externalBusinessID !== json.psFacebookExternalBusinessId
    ) {
      throw new Error('External business ID does not match the one used during the last onboarding. Please redo the process to fix this issue.');
    }

    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'user',
      newState: json.contextPsFacebook.user,
    });
    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'pixel',
      newState: json.contextPsFacebook.pixel,
    });
    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'facebookBusinessManager',
      newState: json.contextPsFacebook.facebookBusinessManager,
    });
    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'page',
      newState: json.contextPsFacebook.page,
    });
    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'ads',
      newState: json.contextPsFacebook.ads,
    });
    commit(MutationsTypes.SET_ONBOARDED_APP, {
      app: 'catalog',
      newState: json.contextPsFacebook.catalog,
    });
  },
};
