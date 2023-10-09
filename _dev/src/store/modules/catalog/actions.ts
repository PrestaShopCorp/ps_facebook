import {ActionContext} from 'vuex';
import {fetchShop} from '@/lib/api/shopClient';
import ActionsTypes from './actions-types';
import MutationsTypes from './mutations-types';
import {runIf} from '@/lib/Promise';
import {CategoryMatchingStatus, ProductFeedReport, State} from './state';
import {FullState} from '@/store';

type Context = ActionContext<State, FullState>;

export default {
  async [ActionsTypes.WARMUP_STORE](
    {dispatch, state, getters}: Context,
  ) {
    if (state.warmedUp) {
      return;
    }
    state.warmedUp = true;

    await runIf(
      !getters.GET_CATALOG_PAGE_ENABLED,
      dispatch(ActionsTypes.REQUEST_SYNCHRONIZATION_STATS),
    );
  },

  async [ActionsTypes.REQUEST_SYNCHRONIZATION_STATS]({commit}: Context) {
    type SynchronizationStatusDto = {
      exportDone: boolean,
      exportOn: boolean,
      matchingDone: boolean,
      matchingProgress: {
        matched: number,
        total: number,
      },
      validation: {
        prevalidation: {
          syncable: number,
          notSyncable: number,
          lastScanDate: string,
        },
        reporting: {
          lastSyncDate: string,
          catalog: number,
          errored: number,
        }
      }
    };

    const result: SynchronizationStatusDto = await fetchShop('CatalogSummary');

    if (result.exportDone) {
      commit(MutationsTypes.SET_CATALOG_PAGE_ENABLED);
    }

    commit(MutationsTypes.SET_SYNCHRONIZATION_ACTIVE, result.exportOn);
    commit(MutationsTypes.SET_SYNCHRONIZATION_SUMMARY, {
      prevalidation: {
        ...result.validation.prevalidation,
        lastScanDate: new Date(result.validation.prevalidation.lastScanDate),
      },
      reporting: {
        ...result.validation.reporting,
        lastSyncDate: new Date(result.validation.reporting.lastSyncDate),
      },
    } as ProductFeedReport,
    );
    commit(MutationsTypes.SET_CATEGORY_MATCHING_SUMMARY, {
      matchingDone: result.matchingDone,
      matchingProgress: result.matchingProgress,
    } as CategoryMatchingStatus);
  },
};
