import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {OnboardingContext} from './state';
import {runIf} from '@/lib/Promise';
import {RequestState} from '@/store/modules/catalog/types';

export default {
  async [ActionsTypes.WARMUP_STORE](
    {dispatch, state, getters},
  ) {
    if ([
      RequestState.PENDING,
      RequestState.SUCCESS,
    ].includes(state.warmedUp)) {
      return;
    }
    state.warmedUp = RequestState.PENDING;

    await runIf(
      !getters.GET_EXTERNAL_BUSINESS_ID,
      dispatch(ActionsTypes.REQUEST_EXTERNAL_BUSINESS_ID),
    );
    await runIf(
      !getters.IS_USER_ONBOARDED,
      dispatch(ActionsTypes.REQUEST_ONBOARDING_STATE),
    );
    state.warmedUp = RequestState.SUCCESS;
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

    commit(MutationsTypes.SET_ONBOARDED_CONTEXT, json.contextPsFacebook);
  },
};
