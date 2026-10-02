import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormLeaveType';
const parentApp = 'Hubleto/App/Community/HrLeave';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormLeaveType = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LeaveType'}
  urlSlug='hr-leave/types'
  title={{field: 'name', sub: T.translate('Leave type')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='name' />
    <Input field='annual_entitlement' />
    <Input field='is_paid' />
    <Input field='requires_approval' />
    <Input field='description' />
  </div>}}}
  {...props}
/>

export default FormLeaveType;