import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormWorkLocation';
const parentApp = 'Hubleto/App/Community/HrEmployees';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormWorkLocation = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/WorkLocation'}
  urlSlug='hr-employees/work-locations'
  title={{field: 'name', sub: T.translate('Work location')}}
  tabs={{default: {content: () => <>
    <Input field='name' />
    <Input field='description' />
  </>}}}
  {...props}
/>

export default FormWorkLocation;
