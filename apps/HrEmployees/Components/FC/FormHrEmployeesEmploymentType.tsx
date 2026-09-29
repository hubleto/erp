import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrEmployeesEmploymentTypeProps extends FormProps {}

const componentName = 'FormHrEmployeesEmploymentType';
const parentApp = 'Hubleto/App/Community/HrEmployees';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrEmployeesEmploymentType = (props: FormHrEmployeesEmploymentTypeProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  urlSlug='hr-employees/employment-types'
  title={{field: 'name', sub: T.translate('Employment type')}}
  tabs={{default: {content: () => <>
    <Input field='name' />
    <Input field='description' />
  </>}}}
  {...props}
/>

export default FormHrEmployeesEmploymentType;
