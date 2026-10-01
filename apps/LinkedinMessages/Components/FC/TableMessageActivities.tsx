import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMessageActivity, { FormMessageActivityProps } from './FormMessageActivity';

interface TableMessageActivitiesProps extends TableProps {
  idMessage?: number,
}

const componentName = 'TableMessageActivities'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableMessageActivities = (props: TableMessageActivitiesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/MessageActivity'}
    endpointParams={{idMessage: props.idMessage}}
    baseUrlSlug='linkedin-messages/followups'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_message: props.idMessage}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMessageActivity {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMessageActivities;
