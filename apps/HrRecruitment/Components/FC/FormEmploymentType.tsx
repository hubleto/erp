import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormEmploymentType';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormEmploymentType = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/EmploymentType'}
  urlSlug='hr-recruitment/employment-types'
  title={{field: 'name', sub: T.translate('Employment type')}}
  tabs={{default: {content: () => <>
    <Input field='name' />
    <Input field='description' />
  </>}}}
  {...props}
/>

export default FormEmploymentType;