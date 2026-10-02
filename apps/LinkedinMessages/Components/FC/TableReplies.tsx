import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormReply, { FormReplyProps } from './FormReply';

interface TableRepliesProps extends TableProps {
  idMessage?: number,
}

const componentName = 'TableReplies'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableReplies = (props: TableRepliesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Reply'}
    endpointParams={{idMessage: props.idMessage}}
    baseUrlSlug='linkedin-messages/replies'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_message: props.idMessage}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormReply {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableReplies;
