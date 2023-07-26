import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {HooksStatuses} from './state';

export default {
  async [ActionsTypes.GET_MODULES_VERSIONS]({commit}, moduleName: string) {
    try {
      const result = await fetchShop('getModuleStatus', {moduleName});
      if (result.hooks) {
        commit(MutationsTypes.SET_HOOKS_STATUS, result.hooks);
      }
      return result;
    } catch (error) {
      console.error(error);
      return error;
    }
  },

  // eslint-disable-next-line no-empty-pattern
  async [ActionsTypes.TRIGGER_REGISTER_HOOK]({}, hookName: string) {
    return fetchShop('registerHook', {hookName});
  },

  async [ActionsTypes.FIX_UNREGISTERED_HOOKS]({state, dispatch}): Promise<void> {
    if (!Object.entries(state.hooks).length) {
      await dispatch(ActionsTypes.GET_MODULES_VERSIONS, 'ps_facebook');
    }

    const hooksNames = Object.keys(state.hooks as HooksStatuses);

    for (let index = 0; index < hooksNames.length; index += 1) {
      const hookName = hooksNames[index];

      if (state.hooks[hookName] === false) {
        /* eslint-disable no-await-in-loop */
        await dispatch(ActionsTypes.TRIGGER_REGISTER_HOOK, hookName);
      }
    }

    await dispatch(ActionsTypes.GET_MODULES_VERSIONS, 'ps_facebook');
  },
};
