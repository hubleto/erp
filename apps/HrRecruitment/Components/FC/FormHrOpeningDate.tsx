import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrOpeningDateProps extends FormProps {}

const componentName = 'FormHrOpeningDate';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrOpeningDate = (props: FormHrOpeningDateProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/OpeningDate'}
  urlSlug='hr-recruitment/opening-dates'
  title={{field: 'name', sub: T.translate('Opening date')}}
  tabs={{default: {content: () => <>
    <Input field='name' />
    <Input field='opened_on' />
  </>}}}
  {...props}
/>

export default FormHrOpeningDate;