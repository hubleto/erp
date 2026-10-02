import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormEmploymentStatus';
const parentApp = 'Hubleto/App/Community/HrEmployees';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormEmploymentStatus = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentStatus'}
  urlSlug='hr-employees/employment-statuses'
  title={{field: 'name', sub: T.translate('Employment status')}}
  tabs={{default: {content: () => <>
    <Input field='name' />
    <Input field='description' />
  </>}}}
  {...props}
/>

export default FormEmploymentStatus;
