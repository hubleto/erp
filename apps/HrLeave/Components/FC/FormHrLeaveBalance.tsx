import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrLeaveBalanceProps extends FormProps {}

const componentName = 'FormHrLeaveBalance';
const parentApp = 'Hubleto/App/Community/HrLeave';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrLeaveBalance = (props: FormHrLeaveBalanceProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LeaveBalance'}
  urlSlug='hr-leave/balances'
  title={{field: 'year', sub: T.translate('Leave entitlement')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='id_leave_type' />
    <Input field='year' />
    <Input field='days_entitled' />
    <Input field='days_carried_over' />
  </div>}}}
  {...props}
/>

export default FormHrLeaveBalance;