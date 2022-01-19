import de from '@/assets/json/translations/de/ui.json';
import en from '@/assets/json/translations/en/ui.json';
import es from '@/assets/json/translations/es/ui.json';
import fr from '@/assets/json/translations/fr/ui.json';
import it from '@/assets/json/translations/it/ui.json';
import nl from '@/assets/json/translations/nl/ui.json';
import pl from '@/assets/json/translations/pl/ui.json';
import pt from '@/assets/json/translations/pt/ui.json';

export const messages = {
  de,
  en,
  es,
  fr,
  it,
  nl,
  pl,
  pt,
};

export const locales = Object.keys(messages);

export default {
  locales,
  messages,
};
