import {State, state as originalState} from './state';
import mutations from './mutations';

describe('Product Feed mutations', () => {
  describe('SAVE_VERIFICATION_ISSUE_PRODUCTS', () => {
    it('Creates the list and adds the new elements', () => {
      const state: State = {
        ...originalState,
      };

      mutations.SET_ONBOARDED_APP(
        state,
        {
          app: 'user',
          newState: {
            email: 'doge-the-dog@perdu.com',
          }
        },
      );

      expect(state.onboarded).toEqual({
        user: {
          email: 'doge-the-dog@perdu.com',
        },
      });
    });
  });
});