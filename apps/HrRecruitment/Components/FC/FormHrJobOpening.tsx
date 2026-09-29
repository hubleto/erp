import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrJobOpeningProps extends FormProps {}

const componentName = 'FormHrJobOpening';
const parentApp = 'Hubleto/App/Community/HrRecruitment';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrJobOpening = (props: FormHrJobOpeningProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/JobOpening'}
  urlSlug='hr-recruitment/job-openings'
  title={{field: 'title', sub: T.translate('Job opening')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='title' />
    <Input field='department' />
    <Input field='id_work_location' />
    <Input field='id_employment_type' />
    <Input field='status' />
    <Input field='positions' />
    <Input field='id_opening_date' />
    <Input field='date_closed' />
    <Input field='id_hiring_manager' />
    <Input field='description' />
  </div>}}}
  {...props}
/>

export default FormHrJobOpening;