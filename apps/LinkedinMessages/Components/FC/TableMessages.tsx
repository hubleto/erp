import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormMessage, { FormMessageProps } from './FormMessage';

interface TableMessagesProps extends TableProps {
  idProfile?: number,
  idCampaign?: number,
  idAccount?: number,
}

const componentName = 'TableMessages'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableMessages = (props: TableMessagesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Message'}
    endpointParams={{idProfile: props.idProfile, idCampaign: props.idCampaign, idAccount: props.idAccount}}
    baseUrlSlug='linkedin-messages'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_profile: props.idProfile, id_campaign: props.idCampaign, id_account: props.idAccount}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS ? data.TAGS.map((tag: any, key: any) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>
        }) : null;
      } else if (columnName == "body") {
        const text: string = data.body ?? '';
        const short = text.length > 140 ? text.substring(0, 140) + '…' : text;
        // unread incoming messages are highlighted
        return <span className={(!data.is_read && data.direction == 1) ? 'font-bold' : ''}>{short}</span>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormMessage {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableMessages;
