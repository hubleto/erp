import Modal from '@hubleto/react-ui/components/fc/Modal';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';
import request from '@hubleto/react-ui/core/Request';
import Translator from '@hubleto/react-ui/core/Translator';
import React, { useState, useEffect } from 'react'
import * as uuid from 'uuid';

interface DashboardProps {
  idDashboard: number,
  redirectUrl: string,
  panels: Array<Panel>,
  showAddNewPanelButton: boolean,
}

export interface Panel {
  id: number,
  width: number,
  title: string,
  board_url_slug: string,
  configuration: any,
  contentLoaded?: boolean,
  content?: string,
}

const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/Dashboard');

const Dashboard = (props: DashboardProps) => {

  const setPanelWidth = (idPanel: number, width: number) => {
    let newPanels = panels;
    for (let i in newPanels) {
      if (newPanels[i].id == idPanel) {
        newPanels[i].width = width;
      }
    }
    setPanels(newPanels);

    request.get(
      'dashboards/api/set-panel-width',
      {
        idDashboard: props.idDashboard,
        idPanel: idPanel,
        width: width,
      },
      (result: any) => {
      }
    );
  }


  const onDragStart = (e: any, idPanel: number) => {
    setDraggedIdPanel(idPanel);
    setTimeout(() => { setHidePanelsWhileDragging(true) }, 50);
    e.dataTransfer.effectAllowed = "move";
  };

  const onDragOver = (e: any, targetPanelId: number) => {
    e.preventDefault();
    if (draggedIdPanel === targetPanelId) return;

    let draggedPanel: Panel = null;
    for (let i in panels) {
      if (panels[i].id === draggedIdPanel) draggedPanel = panels[i];
    }

    const newPanels = [];
    for (let i in panels) {
      if (panels[i].id == draggedIdPanel) continue;
      if (panels[i].id == targetPanelId) {
        newPanels.push(draggedPanel);
      }
      newPanels.push(panels[i]);
    }

    setPanels(newPanels);
  };

  const onDrop = (e: any) => {
    e.preventDefault();

    request.get(
      'dashboards/api/sort-panels',
      {
        idDashboard: props.idDashboard,
        idPanelsSorted: panels.map((item: Panel) => item.id),
      },
      (html: any) => {
        setDraggedIdPanel(null);
        setHidePanelsWhileDragging(false);
      }
    );
  };

  const [panels, setPanels] = useState(props.panels);
  const [draggedIdPanel, setDraggedIdPanel] = useState(0);
  const [hidePanelsWhileDragging, setHidePanelsWhileDragging] = useState(false);

  useEffect(() => {
    for (let i in panels) {
      const panel = panels[i];

      let configuration: any = {};

      try {
        configuration = JSON.parse(panel.configuration ?? '');
      } catch (ex) {
        configuration = {};
      }

      configuration.idPanel = panel.id;
      configuration.panelUrlSlug = panel.board_url_slug;
      configuration.panelUid = uuid.v4();

      if (!panel.contentLoaded) {
        request.post(
          panel.board_url_slug,
          configuration ?? {},
          {},
          (html: any) => {
            let newPanels = panels;
            try {
              newPanels[i].contentLoaded = true;
              newPanels[i].content = html;
              setPanels([...newPanels]);
            } catch (err) {
              console.error(err);
            }
          }
        );
      }
    }
  }, []);

  return <div className='flex flex-col gap-2'>
    <div className='flex flex-row justify-between'>
      <a
        className='btn btn-transparent'
        href={"dashboards/" + props.idDashboard}
      >
        <span className="icon"><i className="fas fa-cog"></i></span>
        <span className="text text-nowrap">{T.translate('Configure this dashboard')}</span>
      </a>
    </div>
    <div className='flex flex-col gap-2 md:grid md:grid-cols-6'>
      {panels.map((panel: Panel, index: any) => {
        const width = panel.width ?? 1;
        return <div
          key={index}
          className={
            "card"
            + " " + (panel.id == draggedIdPanel ? "card-info" : "")
          }
          style={{gridColumn: `span ${width}`}}
        >
          <div
            className="card-header cursor-move"
            draggable
            onDragStart={(e: any) => onDragStart(e, panel.id)}
            onDragOver={(e: any) => onDragOver(e, panel.id)}
            onDrop={(e: any) => onDrop(e)}
          >
            <div className='btn-group items-center hidden md:block'>
              <button
                className='btn btn-transparent btn-small'
                onClick={() => {
                  let newWidth = (panel.width ?? 3) - 1;
                  if (newWidth > 6) newWidth = 6;
                  if (newWidth < 1) newWidth = 1;
                  setPanelWidth(panel.id, newWidth);
                }}
              >
                <span className='icon'><i className='fas fa-minus'></i></span>
              </button>
              <button
                className='btn btn-transparent btn-small'
                onClick={() => {
                  let newWidth = (panel.width ?? 3) + 1;
                  if (newWidth > 6) newWidth = 6;
                  if (newWidth < 1) newWidth = 1;
                  setPanelWidth(panel.id, newWidth);
                }}
              >
                <span className='icon'><i className='fas fa-plus'></i></span>
              </button>
            </div>
            {panel.title}
          </div>
          {hidePanelsWhileDragging ?
            <div className="card-body bg-gray-50 p-4"></div>
          : (panel.contentLoaded ?
            <div className="card-body" dangerouslySetInnerHTML={{__html: panel.content}}></div>
          :
            <div className="card-body">
              <Spinner size="sm" />
            </div>
          )}
        </div>
      })}
    </div>
  </div>

}

export default Dashboard;